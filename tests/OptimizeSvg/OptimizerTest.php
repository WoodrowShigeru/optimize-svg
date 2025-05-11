<?php
/**
 * OptimizerTest testing class file.
 *
 * @author Woodrow Shigeru <woodrow.shigeru@gmx.net>
 * @since 0.0.2
 */

namespace Testing\Toolbox\Strings;

use OptimizeSvg\Optimizer;
use PHPUnit\Framework\Testcase;


/**
 * OptimizerTest testing class.
 *
 * @author Woodrow Shigeru <woodrow.shigeru@gmx.net>
 * @since 0.0.2
 */
final class OptimizerTest extends Testcase {

	
	// ---------------------- ALPHA + OMEGA -----------------------------------

	/**
	 * TODO  -- Test the conversion of an input word to Title Case.
	 *
	 * [DONT_TYPEHINT_TEST_ARGUMENTS]
	 *
	 * @return void
	 */
	public function setUp(): void {

		// PSEUDOCODE:

		$project_root = '?';

		$dir = resolve_dotdots($project_root .DIRECTORY_SEPARATOR .'test-svgs');

		// if exists.
		rmdir($dir);  // but must be empty :^(

		mkdir($dir);

		// begin writing SVG content into files.
		// […]
	}



	// ---------------------- * -----------------------------------------------
	// […]
}

