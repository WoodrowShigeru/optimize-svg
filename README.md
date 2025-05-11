
# ReadMe

## What it does

Reduces filesize by removing unnecessary characters.

* Remove elements that are hidden (`display:none`).
* Remove whitespace between nodes.

Example input:

```html
<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<svg width="200" height="200" version="1.1" viewBox="0 0 52.917 52.917" xmlns="http://www.w3.org/2000/svg">
 <g style="display:none">
  <circle cx="9.3003" cy="13.269" r="9.3003" style="fill:#ff4bf5;stroke-width:0"/>
 </g>
 <ellipse cx="31.75" cy="23.371" rx="21.167" ry="16.492" style="fill:#00f;stroke-width:0"/>
 <g style="display:none">
  <circle cx="12.27" cy="40.845" r="6.4495" style="fill:#ff0;stroke-width:0"/>
 </g>
</svg>
```

Output:

```html
<?xml version="1.0" encoding="UTF-8"?>
<!-- Created with Inkscape (http://www.inkscape.org/) -->
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" version="1.1" viewBox="0 0 52.917 52.917"><ellipse cx="31.75" cy="23.371" rx="21.167" ry="16.492" style="fill:#00f;stroke-width:0"/></svg>
```


　​

## Installation

Clone repository or download a release file.

```php
<?php
// recommended:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// necessary:
require_once '{path/to/this/repo}/src/classes/Optimizer.php';
```

Or alternatively, use namespaces:

```php
<?php

use OptimizeSvg\Optimizer;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '{path/to/this/repo}/autoload.php';
```


　​

## Usage

**Single file:** <br />

```php
<?php

$input_file = '{path}/unoptimized.svg';
$output_file = '{path}/optimized.svg';

$optimizer = new Optimizer($input_file, $output_file);
$optimizer->optimize();

// if you want to commit to writing the optimized file …
$optimizer->save();

// … else if you just want to render it here.
echo $optimizer->dump();
```


　​

**Directories:** <br />
You can use a directory with many \*.SVG files as input – you just have to do it a certain way.

```php
<?php
// ✖ will throw an Exception.
$optimizer = new Optimizer('./unoptimized-dir', './output-dir');

// ✔ please use it like this.
Optimizer::processDir('./unoptimized-dir', './output-dir');
```


　​

**Terminal:** <br />
You can also do it from the shell context with PHP installed.

If any, write the configuration in kebab-case as one comma-concatenated string.

Multiple examples:

```bash
# base usage principle.
$ php -f {path}/optimize-svg/cli.php ./unoptimized.svg ./clean.svg

# possibly, file permission shenanigans.
$ sudo -uwww-data php -f {path}/optimize-svg/cli.php  ./unoptimized.svg  ./clean.svg

# directory.
$ sudo -uwww-data php -f {path}/optimize-svg/cli.php  ./unoptimized-dir  ./output-dir

# configurations.
$ sudo -uwww-data php -f {path}/optimize-svg/cli.php  \
 ./unoptimized.svg  ./clean.svg  keep-whitespace

$ sudo -uwww-data php -f {path}/optimize-svg/cli.php  \
 ./unoptimized.svg  ./clean.svg  keep-whitespace,keep-hidden-nodes

```


　​

## Configuration

`CONFIG_KEEP_HIDDEN_NODES`
: Do not remove hidden nodes.

`CONFIG_KEEP_WHITESPACE`
: Do note remove whitespace between nodes.

```php
<?php

$optimizer = new Optimizer(
	$input_file,
	$output_file,
	Optimizer::CONFIG_KEEP_HIDDEN_NODES | Optimizer::CONFIG_KEEP_WHITESPACE
);
```


　​

## What it can't do

* Detect whether an element is hidden only via a computed property; for example:

```html
<style>.hidden{display:none}</style>
<g class="hidden">…</g>
```


　​

## Dependencies

* PHP7 — Modules: dom, mbstring, xml.


　​

