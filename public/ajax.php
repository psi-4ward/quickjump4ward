<?php

/**
 * Quickjump4ward
 * Quickly jump to various contao pages and settings
 *
 * @copyright  4ward.media 2014 <http://www.4wardmedia.de>
 * @author     Christoph Wiechert <wio@psitrax.de>
 * @package    quickjump4ward
 * @license    LGPL
 * @filesource
 */


/**
 * Initialize the system
 */
$dir = __DIR__;

while ($dir != '.' && $dir != '/' && !is_file($dir . '/system/initialize.php')) {
	$dir = dirname($dir);
}

if (!is_file($dir . '/system/initialize.php')) {
	echo 'Could not find initialize.php!';
	exit(1);
}

define('TL_MODE', 'BE');
require($dir . '/system/initialize.php');

// return the results as json
$x = new \Quickjump4ward\Quickjump4ward();
echo json_encode($x->getResult(Input::post('s'),Input::post('get')));

