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

// Insert javascripts
if(TL_MODE == 'BE')
{
$GLOBALS['TL_JAVASCRIPT']['autocompleter'] = 'plugins/autocompleter/js/ac_compress.js';
$GLOBALS['TL_JAVASCRIPT']['quickjump4ward'] = 'system/modules/quickjump4ward/html/quickjump4ward.js';

$GLOBALS['TL_CSS']['quickjump4ward'] = 'system/modules/quickjump4ward/html/quickjump4ward.css';
}
?>