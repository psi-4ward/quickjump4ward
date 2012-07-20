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


/**
 * Initialize the system
 */
define('TL_MODE', 'BE');
define('BYPASS_TOKEN_CHECK',true);
require_once('../../initialize.php');

$input = Input::getInstance();

// return the results as json
$x = new Quickjump4ward();
echo json_encode($x->getResult($input->post('s'),$input->post('get')));

