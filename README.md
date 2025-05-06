
# ReadMe

TODO


　​

## Dev

* Install LAMP or similar.
* Need WoodrowShigeru/InteractiveVarDump (↯) (or keep-deactivate its usages).
* `$ sudo ln -s /home/$USER/work/optimize-svg /var/www/lamp.local/public_html/optimize-svg`
* → http://lamp.local/optimize-svg


　​

## Installation


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

## Dependencies

* PHP8


　​

