<?php
/**
 * Command-line interface.
 *
 * @since 0.0.2
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */


use OptimizeSvg\Optimizer;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once dirname(__FILE__) .DIRECTORY_SEPARATOR .'autoload.php';


list($script, $input, $output, $flags) = array_pad($argv, 4, NULL);


// config.
$flags = explode(',', strtoupper($flags ?? ''));

$config = 0;

if (in_array('KEEP-HIDDEN-NODES', $flags, TRUE)) {
	$config = $config | Optimizer::CONFIG_KEEP_HIDDEN_NODES;
}

if (in_array('KEEP-WHITESPACE', $flags, TRUE)) {
	$config = $config | Optimizer::CONFIG_KEEP_WHITESPACE;
}

if (in_array('KEEP-NAMESPACES', $flags, TRUE)) {
	$config = $config | Optimizer::CONFIG_KEEP_NAMESPACES;
}



// dir branch.
if (is_dir($input)) {
	Optimizer::processDir($input, $output, $config);

	// echo "Processed directory.\n";
	exit(0);
}



// file branch.
$optimizer = new Optimizer($input, $output, $config);
$optimizer->optimize();
$optimizer->save();

// echo "Processed file.\n";
exit(0);

