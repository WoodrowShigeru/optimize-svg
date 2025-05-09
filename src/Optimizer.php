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


require_once dirname(__FILE__) .DIRECTORY_SEPARATOR .'banans.php';


/**
 * Optimize a single SVG file.
 *
 * @since 0.0.1
 */
class Optimizer {

	const CONFIG_KEEP_HIDDEN_NODES	= 0b001;
	const CONFIG_KEEP_WHITESPACE	= 0b010;

	const SKIPPABLES = ['.', '..'];


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

	/**
	 * @var int $options
	 *   Store the passed options.
	 */
	private $options = 0;



	// ---------------------- ALPHA + OMEGA ------------------------------------

	/**
	 * Create an instance of this file.
	 *
	 * @param string $input
	 * @param string $output
	 * @param int $options
	 *
	 * @throws Exception
	 *   InputFileNotFound
	 *   InputFileNotReadable
	 *   InputFileNoFile
	 *   InputFileNoSvg
	 *   InputFileNoCleanSvg
	 */
	public function __construct( string $input, string $output, int $options = 0 ) {

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
		$this->options = $options;

		$load_options = 0;

		if (!($options & self::CONFIG_KEEP_WHITESPACE)) {
			$load_options = $load_options | LIBXML_NOBLANKS;
		}

		$this->dom = new DOMDocument();
		$this->dom->load($input, $load_options);

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

		if (!($this->options & self::CONFIG_KEEP_HIDDEN_NODES)) {
			$this->removeHiddenChildren($node);
		}

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

		if (
			file_exists($this->output_file)
		&&	!is_writable($this->output_file)
		) {
			throw new Exception('OutputFileNotWritable');
		}


		$content = FALSE;
		$options = 0;
		// TODO  testing: LIBXML_NSCLEAN.

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
	 * @param string $input
	 * @param string $output
	 * @param int $options
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public static function processDir( string $input, string $output, int $options = 0 ) {

	//	return;
		// PSEUDOCODE:


//		if (!file_exists($input)) {
//			throw new Exception('InputDirNotFound');
//		}
//
//		if (!is_readable($input)) {
//			throw new Exception('InputDirNotReadable');
//		}
//
//		if (!is_dir($input)) {
//			throw new Exception('InputDirNoDir');
//		}
//
//
//		if (!is_dir($output)) {
//			throw new Exception('OutputDirNoDir');
//		}
//
//
//		// that method doesn't exist, I think.
//	//	if (!is_writable($output)) {
//	//		throw new Exception('OutputDirNotWritable');
//	//	}
//
//		// that's not how you check that.
//	//	if (!empty($output)) {
//	//		throw new Exception('OutputDirNotEmpty');
//	//	}

		// maybe mkdir.


		$output = provideTrailingSlash($output);



		// TODO  directory traversal?
		$list = listDir($input, [
		//	'plz_files_only' => TRUE,
			'extensions'	=> ['svg'],
		]);
		ivd($list, 'scanned');

	//	throw new Exception('StopHere');

		foreach ($list as $input_file) {
			$output_file = $output .basename($input_file);
		//	ivd($output);
		//	ivd(basename($input_file));
		//	$output_file = magic($input_file, $output);

			$optimizer = new Optimizer($input_file, $output_file, $options);
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


	/**
	 * @return int
	 */
	public function getOptions() {
		return $this->options;
	}
}

