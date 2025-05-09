<?php
/**
 * Optimizer class file.
 *
 * @since 0.0.1
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */

// namespace OptimizeSvg;

// use DOMDocument;
// use DOMElement;
// use Exception;


/**
 * Optimize a single SVG file.
 *
 * @since 0.0.1
 */
class Optimizer {

	// obsolete.
	private $is_dev;

	/**
	 * @var string $input_file
	 *   Absolute full path of the input filename, from which we build our DOM.
	 */
	private $input_file;

	/**
	 * @var string $output_file
	 *   Absolute full path of the output filename, from which we build our DOM.
	 */
	private $output_file;

	/**
	 * @var DOMDocument $dom
	 *   Tree representation of the SVG XML DOM.
	 */
	private $dom;

	/**
	 * @var DOMElement $root
	 *   The root node of the DOM tree.
	 */
	private $root;



	// ---------------------- ALPHA + OMEGA ------------------------------------

	/**
	 * Create an instance of this file.
	 *
	 * @param string $input
	 * @param string $output
	 * @param bool $is_dev
	 *
	 * @throws Exception
	 *   InputFileNotFound
	 *   InputFileNotReadable
	 *   InputFileNoFile
	 *   InputFileNoSvg
	 *   InputFileNoCleanSvg
	 */
	public function __construct( string $input, string $output, bool $is_dev = FALSE ) {

		$this->is_dev = $is_dev;

		if (!file_exists($input)) {
			throw new Exception('InputFileNotFound');
		}

		if (!is_readable($input)) {
			throw new Exception('InputFileNotReadable');
		}

		if (is_dir($input)) {
			throw new Exception('InputFileNoFile');
		}


		$ext = strtoupper(pathinfo($input, PATHINFO_EXTENSION));

		if ($ext !== 'SVG') {
			throw new Exception('InputFileNoSvg');
		}

		// decision: allow $output === $input.


		$this->input_file = $input;
		$this->output_file = $output;


		$this->dom = new DOMDocument();
		$this->dom->load($input);

		$svg = $this->dom->getElementsByTagName('svg');

		if ($svg->length !== 1) {
			throw new Exception('InputFileNoCleanSvg');
		}

		$this->root = $svg[0];
	}



	// ---------------------- CORE ---------------------------------------------

	/**
	 * Remove children from a given node if they are hidden.
	 *
	 * @param DOMElement|DOMText|DOMDocument $node
	 *
	 * @return void
	 */
	private function removeHiddenChildren( $node ) {

		if (!($node instanceof DOMElement)) {
			return;
		}


		$deletables = array_filter(
			iterator_to_array($node->childNodes),
			function($node) {
				// skip check on irrelephant content, i.e. text nodes.
				if (!($node instanceof DOMElement)) {
					return false;
				}

				// hidden via attribute.
				if (
					$node->hasAttribute('display')
				&&	$node->getAttribute('display') === 'none'
				) {
					return true;
				}

				// last chance: inline style.
				return preg_match('/(^|;)display:\s*none(;|$)/', $node->getAttribute('style')) === 1;
			}
		);

		// must first gather, then remove separately, in order to avoid
		// reference errors.
		foreach ($deletables as $child) {
			try {
				$child->parentNode->removeChild($child);

			} catch (Exception $ex) {
				// technically, not possible. But for the IDEs …
			}
		}
	}


	/**
	 * Recursively traverse and optimize a node and its children.
	 *
	 * @param DOMElement|DOMText|DOMDocument $node
	 *
	 * @return void
	 */
	private function recurse( $node ) {

		if (!($node instanceof DOMElement)) {
			return;
		}

		if (!$node->childNodes->length) {
			return;
		}

		$this->removeHiddenChildren($node);

		foreach ($node->childNodes as $child) {
			$this->recurse($child);
		};
	}


	/**
	 * Optimize the full document.
	 *
	 * @return void
	 */
	public function optimize() {

		$this->recurse($this->root);

		// TODO  remove superfluous whitespace (also in <style> block).
		// TODO  maybe optionally add header now. (for licensing?)
		// TODO  multi files / input dir.
	}


	/**
	 * It's a dev thing. You wouldn't understand …
	 *
	 * @throws Exception
	 *   RenderFailed
	 *
	 * @return string
	 *   Returns HTML content of whole document.
	 */
	public function dump() {

		$html = $this->dom->saveHTML();

		if ($html === FALSE) {
			throw new Exception('RenderFailed');
		}

		return $html;
	}


	/**
	 * Save the document in the previously specified output filename.
	 *
	 * @throws Exception
	 *   OutputFileNotWritable
	 *   OutputXmlKaputt
	 *   SaveFailed
	 *
	 * @return void
	 */
	public function save() {

		if (!is_writable($this->output_file)) {
			throw new Exception('OutputFileNotWritable');
		}


		$content = FALSE;
		$options = 0;
		// $options = LIBXML_NOBLANKS;  // TODO  do I have to do this on-load?
		// TODO  testing: LIBXML_NOBLANKS, LIBXML_NSCLEAN.

		try {
			$content = $this->dom->saveXML(NULL, $options);

		} catch (Exception $ex) {
			$content = FALSE;
		}

		if ($content === FALSE) {
			throw new Exception('OutputXmlKaputt');
		}

		if (!file_put_contents($this->output_file, $content)) {
			throw new Exception('SaveFailed');
		}
	}


	/**
	 * @throws Exception
	 *
	 * @param string $input
	 * @param string $output
	 *
	 * @return void
	 */
	public static function processDir( string $input, string $output ) {

		return;
		// PSEUDOCODE:


		$list = scan_somehow($input);
		if (!is_dir($input)) {
			throw new Exception('InputDirNoDir');
		}
		if (!is_readable($input)) {
			throw new Exception('InputDirNotReadable');
		}

		if (!is_dir($output)) {
			throw new Exception('OutputDirNoDir');
		}

		// TODO  directory traversal?


		// that method doesn't exist, I think.
	//	if (!is_writable($output)) {
	//		throw new Exception('OutputDirNotWritable');
	//	}

		// that's not how you check that.
	//	if (!empty($output)) {
	//		throw new Exception('OutputDirNotEmpty');
	//	}

		// maybe mkdir.

		foreach ($input as $input_file) {
			$output_file = magic($input_file, $output);

			$optimizer = new Optimizer($input_file, $output_file, TRUE);
			$optimizer->optimize();
			ivd($optimizer->dump());
			$optimizer->save();
		}
	}



	// ---------------------- GETTER + SETTER ----------------------------------

	/**
	 * @return string
	 */
	public function getInputFile() {
		return $this->input_file;
	}


	/**
	 * @return string
	 */
	public function getOutputFile() {
		return $this->output_file;
	}
}

