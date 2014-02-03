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


// Insert javascripts
if(TL_MODE == 'BE' && \Input::get('do') != 'repository_manager' && \Input::get('do') != 'composer')
{
	$GLOBALS['TL_HOOKS']['getUserNavigation'][] = array('\Quickjump4ward\Quickjump4ward', 'injectJavascript');
}