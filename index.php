<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once implode(DIRECTORY_SEPARATOR, [
	$_SERVER['DOCUMENT_ROOT'],
	'..',
	'ivd',
	'autoload.php',
]);


$input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'tiles.svg']);
$output_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);

ivd([
	'file-exists'	=> file_exists($input_file),
	'readable'		=> is_readable($input_file),
]);


// -----

function remove_hidden_stuff_from_nodelist( $node ) {

	$homunculus = new DOMNode();

	if (!($node instanceof DOMElement)) {
		return;
		return $homunculus;
	}

	$deletables = [];

	foreach ($node->childNodes as $child) {
		$is_hidden = $child->hasAttribute('display') && $child->getAttribute('display') === 'none';
		// ivd(compact('is_hidden'));

		if ($is_hidden) {
			$deletables[] = $child;
		}
	}

	foreach ($deletables as $child) {
		$child->parentNode->removeChild($child);
	}

	return;
	return $homunculus;
}


function funky_recursion( $node ) {

	// TODO  node or element?

	ivd(get_class($node), "isntanceof");

	if (!($node instanceof DOMElement)) {
		return;
		return new DOMElement();
	}

	if (!$node->childNodes->length) {
		return;
	}

	remove_hidden_stuff_from_nodelist($node);

	foreach ($node->childNodes as $child) {
		funky_recursion($child);
	};
}


// -----

$dom = new DOMDocument();

$dom->load($input_file);

$root = $dom->getElementsByTagName('svg');

if ($root->length !== 1) {
	throw new Exception('NoCleanSvg');
}



ivd($root[0]->childNodes->length, "before");

// ivd($root[0]->childNodes[1]->childNodes[1]->childNodes[0]->hasAttribute('display'), "wandering if");

funky_recursion($root[0]);
// $samba = remove_hidden_stuff_from_nodelist($root[0]);

$samba = $root[0];
ivd($samba->childNodes->length, "after");

ivd($dom->saveHTML());

// include $input_file;

