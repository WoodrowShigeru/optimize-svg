<?php
/**
 * Dev environment interface.
 *
 * At this point, merely like the CLI, but with IVD support.
 *
 * @since 0.0.1
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */

use OptimizeSvg\Optimizer;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// IVD.
require_once implode(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], '..', 'ivd', 'autoload.php']);

// optimizer.
// require_once implode(DIRECTORY_SEPARATOR, ['.', 'src', 'classes', 'Optimizer.php']);
require_once implode(DIRECTORY_SEPARATOR, ['.', 'autoload.php']);


$config = 0;
// $config = $config | Optimizer::CONFIG_KEEP_HIDDEN_NODES;
// $config = $config | Optimizer::CONFIG_KEEP_WHITESPACE;


if ($devswitch_test_dirs = TRUE) {
	// DIRECTORY.

	$input  = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles']);
	$output = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'outdir']);

	Optimizer::processDir($input, $output, $config);

	echo "Processed directory.";

	exit;


} else {
	// FILE.

	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'tiles.svg']);
	$input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'dice-6.svg']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'pink.svg']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'fileperm']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'locked']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'excited-friend.✔']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'nosvg.md']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'sneaky.svg']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'half.svg']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'double.svg']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'empty.svg']);
	// $input = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);
	// $input  = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'blue.svg']);
	$output = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);
	// $output = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'locked']);


	// file testing.
	$optimizer = new Optimizer($input, $output, $config);
	$optimizer->optimize();
	ivd($optimizer->dump());
	$optimizer->save();
}

