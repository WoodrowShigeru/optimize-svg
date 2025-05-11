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
$flags = mb_strtoupper($flags, 'UTF-8');
$flags = preg_filter('/-/', '_', $flags) ?? $flags;
$flags = explode(',', $flags);

$config = 0;

foreach ($flags as $flag) {
	$const = sprintf('%s::CONFIG_%s', Optimizer::class, $flag);
	if (defined($const)) {
		$config = $config | constant($const);
	}
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

