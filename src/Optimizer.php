<?php
/**
 * dingdong class file.
 *
 * @since 0.0.1
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */

// namespace OptimizeSvg;

// use DOMDocument;
// use DOMElement;
// use Exception;


/**
 * @since 0.0.1
 */
class Optimizer {

	private $is_dev;
	private $input_file;
	private $output_file;

	private $dom;
	private $root;


	/**
	 * Create an instance of this file.
	 *
	 * @param string $input
	 * @param string $output
	 * @param bool $is_dev
	 *
	 * @throws Exception
	 */
	public function __construct( string $input, string $output, bool $is_dev ) {

		$this->is_dev = $is_dev;

		// TODO  are both necessary?
		if (!file_exists($input) || !is_readable($input)) {
			throw new Exception('InputFileNotReadable');
		}

		// TODO  finish … wrong location?
	//	if (!file_exists($output) || !is_readable($output)) {
	//		throw new Exception('OutputFileNotWritable');
	//	}


		$this->input_file = $input;  // discard?
		$this->output_file = $output;


		$this->dom = new DOMDocument();

		$this->dom->load($input);
		// TODO  trycatch.

		$svg = $this->dom->getElementsByTagName('svg');

		if ($svg->length !== 1) {
			throw new Exception('NoCleanSvg');
		}

		$this->root = $svg[0];
	}



	// -----

	/**
	 * @param DOMElement $node
	 * @return void
	 */
	private function removeHiddenChildren( $node ) {

		if (!($node instanceof DOMElement)) {
			return;
		}


		$deletables = array_filter(
			iterator_to_array($node->childNodes),
			function($node) {
				return
					$node instanceof DOMElement
				&&	$node->hasAttribute('display')
				&&	$node->getAttribute('display') === 'none'
				;
			}
		);

		foreach ($deletables as $child) {
			$child->parentNode->removeChild($child);
		}
	}


	/**
	 * @param DOMElement $node
	 * @return void
	 */
	private function recurse( $node ) {

		// TODO  node or element?

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


	// -----




	/**
	 * @throws Exception
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
	 * @return string
	 *   Returns HTML content of whole document.
	 */
	public function dump() {

		return $this->dom->saveHTML();
	}


	/**
	 * @throws Exception
	 *
	 * @return void
	 */
	public function save() {

		$content = $this->dom->saveXML();

		if (FALSE) {
			// is FALSE?
			throw new Exception('XmlKaputt');
		}

		file_put_contents($this->output_file, $content);
		// TODO  and/or here?
	}







	// GETTERS


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

