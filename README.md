
# ReadMe

TODO


　​

## Dev

* Install LAMP or provide PHP in a way that works for you.
* Need WoodrowShigeru/InteractiveVarDump (↯) (or keep-deactivate its usages).
* `$ sudo ln -s /home/$USER/work/optimize-svg /var/www/lamp.local/public_html/optimize-svg`
* → http://lamp.local/optimize-svg


　​

## Installation


　​

## What it does

* Remove elements that are hidden (`display:none`).
* Remove whitespace between nodes (work in progress).

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
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" version="1.1" viewBox="0 0 52.917 52.917">

 <ellipse cx="31.75" cy="23.371" rx="21.167" ry="16.492" style="fill:#00f;stroke-width:0"/>

</svg>
```

TODO  update?


　​

## Usage



```php
<?php
// recommended:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// necessary:
require_once '{path/to/optimize-svg}/src/Optimizer.php';

// make sure dir is readable/writable.
$input_file = '{path}/unoptimized.svg';
$output_file = '{path}/optimized.svg';

$optimizer = new Optimizer($input_file, $output_file);
$optimizer->optimize();

// if you want to commit to writing the optimized file …
$optimizer->save();

// … else, if you just want to render it here.
echo $optimizer->dump();
```


　​

## Directories

You can use a directory with many \*.SVG files as input (TODO  work in progress) – you just have to do it a certain way.

```php
<?php
// ✖ will throw an Exception.
$optimizer = new Optimizer('unoptimized-file.svg', 'output-file.svg');

// ✔ maybe like this?
Optimizer::processDir('unoptimized-dir', 'output-dir');
```


　​

## What it can't do

* Detect if an element is hidden only via a computed property; for example:

```html
<style>.hidden{display:none}</style>
<g class="hidden">…</g>
```


　​

## Dependencies

* PHP7


　​

