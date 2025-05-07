<?php
/**
 * Dev environment interface.
 *
 * @since 0.0.1
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */

// use OptimizeSvg\Optimizer;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once implode(DIRECTORY_SEPARATOR, [
	$_SERVER['DOCUMENT_ROOT'],
	'..',
	'ivd',
	'autoload.php',
]);

require_once implode(DIRECTORY_SEPARATOR, ['.', 'src', 'Optimizer.php']);


// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'tiles.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'dice-6.svg']);
$input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'pink.svg']);
$output_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);


$optimizer = new Optimizer($input_file, $output_file, TRUE);
$optimizer->optimize();
ivd($optimizer->dump());
$optimizer->save();

