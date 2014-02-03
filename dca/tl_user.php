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


// Fields
$GLOBALS['TL_DCA']['tl_user']['fields']['quickjump4ward_enabled'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['quickjump4ward_enabled'],
	'default'				  => '1',
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_user']['fields']['quickjump4ward'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['quickjump4ward'],
	'default'				  => array('article','page','module'),
	'inputType'               => 'checkbox',
	'options'				  => array('article','page','module','stylesheet','pagelayout'),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_user']['quickjump4wardOptions'],
	'eval'                    => array('multiple'=>true),
	'sql'					  => 'blob NULL'
);

$GLOBALS['TL_DCA']['tl_user']['fields']['quickjump4ward_key_modifier'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['quickjump4ward_key_modifier'],
	'default'   => 'alt',
	'options'   => array('alt', 'control', 'shift', 'meta'),
	'inputType' => 'select',
	'eval'      => array('mandatory'=>true, 'tl_class' => 'w50'),
	'sql'       => "varchar(32) NOT NULL default 'alt'"
);
$GLOBALS['TL_DCA']['tl_user']['fields']['quickjump4ward_key'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['quickjump4ward_key'],
	'default'   => 'q',
	'inputType' => 'text',
	'eval'      => array('mandatory'=>true, 'maxlength'=>1, 'tl_class'=>'w50'),
	'sql'       => "char(1) NOT NULL default 'q'"
);


// Palettes
$GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'quickjump4ward_enabled';
$GLOBALS['TL_DCA']['tl_user']['subpalettes']['quickjump4ward_enabled'] = 'quickjump4ward,quickjump4ward_key_modifier,quickjump4ward_key';

foreach($GLOBALS['TL_DCA']['tl_user']['palettes'] as $k => $v)
{
	if($k == '__selector__') continue;
	$GLOBALS['TL_DCA']['tl_user']['palettes'][$k] = str_replace(';{password_legend',';{quickjump4ward_legend},quickjump4ward_enabled;{password_legend',$v);
}
