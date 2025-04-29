<?php
/**
 * Dev environment interface.
 *
 * @since 0.0.1
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once implode(DIRECTORY_SEPARATOR, [
	$_SERVER['DOCUMENT_ROOT'],
	'..',
	'ivd',
	'autoload.php',
]);


// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'tiles.svg']);
$input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'dice-6.svg']);
$output_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);

ivd([
	'file-exists'	=> file_exists($input_file),
	'readable'		=> is_readable($input_file),
]);

if (FALSE) {
	throw new Exception('InputFileNotReadable');
}


// -----

/**
 * @param DOMElement $node
 * @return void
 */
function remove_hidden_children( $node ) {

	$homunculus = new DOMNode();

	if (!($node instanceof DOMElement)) {
		return;
		return $homunculus;
	}


	$deletables = array_filter(
		iterator_to_array($node->childNodes),
		fn($node) => $node instanceof DOMElement
		&&	$node->hasAttribute('display')
		&&	$node->getAttribute('display') === 'none'
	);

	foreach ($deletables as $child) {
		$child->parentNode->removeChild($child);
	}

	return;
	return $homunculus;
}


/**
 * @param DOMElement $node
 * @return void
 */
function funky_recursion( $node ) {

	// TODO  node or element?

	if (!($node instanceof DOMElement)) {
		return;
		return new DOMElement();
	}

	if (!$node->childNodes->length) {
		return;
	}

	remove_hidden_children($node);

	foreach ($node->childNodes as $child) {
		funky_recursion($child);
	};
}


// -----

$dom = new DOMDocument();

$dom->load($input_file);
// TODO  trycatch.

$root = $dom->getElementsByTagName('svg');

if ($root->length !== 1) {
	throw new Exception('NoCleanSvg');
}



ivd($root[0]->childNodes->length, "before");

funky_recursion($root[0]);
// $samba = remove_hidden_children($root[0]);

$samba = $root[0];
ivd($samba->childNodes->length, "after");

ivd($dom->saveHTML());

file_put_contents($output_file, $dom->saveXML());
// include $input_file;

