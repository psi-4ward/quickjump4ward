<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  4ward.media 2010
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
	'eval'                    => array('multiple'=>true)
);

foreach($GLOBALS['TL_DCA']['tl_user']['palettes'] as $k => $v)
{
	if($k == '__selector__') continue;
	$GLOBALS['TL_DCA']['tl_user']['palettes'][$k] = str_replace(';{theme_legend',';{quickjump4ward_legend},quickjump4ward;{theme_legend',$v);
}


?>