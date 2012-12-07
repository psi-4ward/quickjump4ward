<?php

/**
 * Quickjump4ward
 * A Contao-Extension to quickly access cartain backend-modules
 * through typing
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
	$GLOBALS['TL_HOOKS']['getUserNavigation'][] = array('\Quickjump4ward\Quickjump4ward','injectJavascript');
}