<?php

/**
 * Quickjump4ward
 *
 * @copyright  4ward.media 2012 <http://www.4wardmedia.de>
 * @author     Christoph Wiechert <christoph.wiechert@4wardmedia.de>
 * @package    quickjump4ward
 * @license    LGPL 
 * @filesource
 */

// Insert javascripts
if(TL_MODE == 'BE')
{
	$GLOBALS['TL_JAVASCRIPT']['autocompleter'] = 'plugins/autocompleter/js/ac_compress.js';
	$GLOBALS['TL_JAVASCRIPT']['quickjump4ward'] = 'system/modules/quickjump4ward/html/quickjump4ward.js';

	$GLOBALS['TL_CSS']['quickjump4ward'] = 'system/modules/quickjump4ward/html/quickjump4ward.css';
}