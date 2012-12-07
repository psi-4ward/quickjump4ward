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


// set the filter for tl_styles
if(Input::get('category'))
{
	$filter = $this->Session->get('filter');
	$filter['tl_style_'.Input::get('id')]['category'] = Input::get('category');
	$this->Session->set('filter',$filter);
	$url = Environment::get('request');
	$url = preg_replace('(&category=[^&]+)','',$url);
	$this->redirect($url);
}
