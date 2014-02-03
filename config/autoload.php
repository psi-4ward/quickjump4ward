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


ClassLoader::addNamespace('Psi');

ClassLoader::addClasses(array
(
	// Models
	'Psi\Quickjump4ward\Quickjump4ward' => 'system/modules/quickjump4ward/Quickjump4ward.php',
));