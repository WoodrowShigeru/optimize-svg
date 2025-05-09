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


// IVD.
require_once implode(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], '..', 'ivd', 'autoload.php']);

// optimizer.
require_once implode(DIRECTORY_SEPARATOR, ['.', 'src', 'Optimizer.php']);


// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'tiles.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'dice-6.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'pink.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'fileperm']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'locked']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'excited-friend.✔']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'nosvg.md']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'sneaky.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'half.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'double.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'empty.svg']);
// $input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);
$input_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'circles', 'blue.svg']);
$output_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'output.svg']);
// $output_file = implode(DIRECTORY_SEPARATOR, ['.', 'examples', 'locked']);

$config = 0;
$config = $config | Optimizer::CONFIG_KEEP_HIDDEN_NODES;
$config = $config | Optimizer::CONFIG_KEEP_WHITESPACE;

$optimizer = new Optimizer($input_file, $output_file, $config);
$optimizer->optimize();
ivd($optimizer->dump());
$optimizer->save();

