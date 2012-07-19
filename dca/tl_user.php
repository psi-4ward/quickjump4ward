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


$GLOBALS['TL_DCA']['tl_user']['fields']['quickjump4ward'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['quickjump4ward'],
	'default'				  => array('article','page','module'),
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'options'				  => array('article','page','module','stylesheet','pagelayout'),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_user']['quickjump4wardOptions'],
	'eval'                    => array('multiple'=>true),
	'sql'					  => 'blob NULL'
);

foreach($GLOBALS['TL_DCA']['tl_user']['palettes'] as $k => $v)
{
	if($k == '__selector__') continue;
	$GLOBALS['TL_DCA']['tl_user']['palettes'][$k] = str_replace(';{theme_legend',';{quickjump4ward_legend},quickjump4ward;{theme_legend',$v);
}
