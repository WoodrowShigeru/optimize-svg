<?php
/**
 * Global declaration of global all-purpose functions.
 *
 * @since 0.0.2
 * @author WoodrowShigeru <woodrow.shigeru@gmx.net>
 */


define('FILE_SYSTEM_ALIASES', ['.', '..']);


/**
 * Resolve any "dotdots" (or double periods) in a given path.
 *
 * (phpDocumentor can't handle them in the summary line) "Dotdots" refers to `..`.
 *
 * This is especially useful for avoiding the confusing behavior `file_exists()`
 * shows with symbolic links.
 *
 * [HIGH_AVAILABILITY]
 *
 * @param string $path
 *
 * @return string
 */
function resolve_dotdots( string $path ) {

	if (empty($path)) {
		return $path;
	}


	$source = array_reverse(explode(DIRECTORY_SEPARATOR, $path));
	$balance = 0;
	$parts = array();

	// going backwards through the path, keep track of the dotdots and "work
	// them off" by skipping a part. Only take over the respective part if the
	// balance is at zero.
	foreach ($source as $part) {
		if ($part === '..') {
			$balance++;

		} else if ($balance > 0) {
			$balance--;

		} else {
			array_push($parts, $part);
		}
	}

	// special case: path begins with too many dotdots, references "outside
	// knowledge".
	if ($balance > 0) {
		for ($i = 0; $i < $balance; $i++) {
			array_push($parts, '..');
		}
	}

	$parts = array_reverse($parts);

	return implode(DIRECTORY_SEPARATOR, $parts);
}


/**
 * Add a trailing forward slash if it's missing.
 *
 * Does not check if the directory exists, or if it's a genuine path in any
 * way, shape or form. Therefore, this method can be used for URLs, too.
 *
 * @param string $dir
 *
 * @return string
 *   Returns potentially altered directory.
 */
function provide_trailing_slash( string $dir ) {

	// Note: this method also works on multibyte strings despite the fact
	//   that it uses non-multibyte string functions, because we are
	//   explicitly searching for and comparing against a specific
	//   character. If the examined character is a garbled mess we can
	//   safely say that it's not "slash".

	if (
		empty($dir)
	||	(strpos(strrev($dir), DIRECTORY_SEPARATOR) !== 0)
	) {
		$dir .= DIRECTORY_SEPARATOR;
	}

	return $dir;
}


/**
 * List the contents of a given directory recursively.
 *
 * Basically, converts a potentially deep file structure into a flat list
 * of locations.
 *
 * @param string $dir
 *
 * @param mixed[]|null $config
 *   Optional configuration:
 *
 *   Name | Type | Description
 *   -----|------|------------
 *   `plz_files_only` | boolean | If enabled, still traverses subdirectories but doesn't list them. Defaults to `FALSE`.
 *   `skip_by_extension` | string[] | List of extensions to skip, if any. Aka extensions blacklist.
 *   `extensions` | string[] | Extensions whitelist, if any.
 *
 * @throws Exception
 *   DirNotFound
 *   OpenDirFailed
 *
 * @return string[]
 *   Returns a list of server-context file locations in no particular
 *   order.
 */
function listDir( string $dir, array $config = NULL ) {

	// TODO  move updated logic to, and use PhpWsh.

	$contents = [];

//	self::preventDirectoryTraversal($dir, 'project_root');
	//
	// not relevant here. In fact, in the way.

	if (!file_exists($dir) || !is_dir($dir)) {
		throw new Exception('DirNotFound—' .$dir);
	}


	// normalize parameters.
	$dir = provide_trailing_slash($dir);

	if (!isset($config)) {
		$config = [];
	}

	if (!isset($config['plz_files_only'])) {
		$config['plz_files_only'] = FALSE;
	}

	$has_whitelist = isset($config['extensions']);
	$has_blacklist = isset($config['skip_by_extension']);
	$has_ext_filter = $has_whitelist || $has_blacklist;

	if (!isset($config['skip_by_extension'])) {
		$config['skip_by_extension'] = [];
	}

	if (!isset($config['extensions'])) {
		$config['extensions'] = [];
	}


	// normalize extension lists, if any.
	if ($has_ext_filter) {
		$filters = ['skip_by_extension', 'extensions'];

		foreach ($filters as $name) {
			$count = count($config[$name]);

			if ($count) {
				$encodings = array_fill(0, $count, 'UTF-8');
				$config[$name] = array_map('mb_strtolower', $config[$name], $encodings);
			}
		}
	}


	// try to open.
	$handle = opendir($dir);

	if ($handle === FALSE) {
		throw new Exception('OpenDirFailed—' .$dir);
	}


	// iterate.
	while ( ($entry = readdir($handle)) !== FALSE ) {
		// TODO  clumsy.
		if (!in_array($entry, FILE_SYSTEM_ALIASES, TRUE)) {
			$ext = mb_strtolower(pathinfo($entry, PATHINFO_EXTENSION), 'UTF-8');
			$combo = $dir .$entry;

			if (is_file($combo)) {
				if (!$has_ext_filter) {
					$contents[] = $combo;

				} else {
					if ($has_whitelist && in_array($ext, $config['extensions'], TRUE)) {
						$contents[] = $combo;

					} else if (
						$has_blacklist && (
							empty($ext)
						||	!in_array($ext, $config['skip_by_extension'], TRUE)
						)
					) {
						// TODO  untested else-if part. Not really needed in this project, anyway.
						$contents[] = $combo;
					}
				}


			} else if (is_dir($combo)) {
				if (!$config['plz_files_only']) {
					$contents[] = provide_trailing_slash($combo);
				}

				// recursion.
				$contents = array_merge($contents, listDir($combo, $config));
			}

			// decision: ignore symbolic links and such.
		}
	}

	// free memory.
	closedir($handle);

	return $contents;
}

