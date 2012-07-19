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


$GLOBALS['TL_DCA']['quickjump4ward']['fields']['Quickjump']['explanation'] = 'quickjump4ward';
$GLOBALS['TL_DCA']['quickjump4ward']['fields']['Quickjump']['label'][0] = 'Quickjump4ward - Suchmodifier';

$GLOBALS['TL_LANG']['XPL']['quickjump4ward'] = array
(
	array('Hotkey', '<p>Mit der Tastenkombination <i>Strg-j</i> oder <i>Strg-q</i> kann direkt ins Quickjump-Feld gesprungen werden.</p>
	<p>Per <i>Tab</i> wird die Vervollständigung erneut angestoßen um ggf. Unterobjekte abzurufen. Bei Problemen mit <i>Tab</i> kann auch die <i>Pfeil-nach-rechts</i> Taste verwendet werden.</p>
	<p>Die <i>Enter</i>-Taste ruft die gewählte Seite auf.</p>'),
	array('<u>p:</u> Seiten', 'Der Modifier <u>p:</u> sucht Seiten aus der Seitenstruktur.'),
	array('<u>m:</u> Module', 'Der Modifier <u>m:</u> sucht in allen verfügbaren Modulen.'),
	array('<u>a:</u> Artikel', 'Der Modifier <u>a:</u> sucht nur Artikel.'),
	array('<u>pl:</u> Seitenlayouts', 'Der Modifier <u>pl:</u> durchsucht die Themes nach Seitenlayouts.'),
	array('<u>css:</u> Stylesheets', 'Der Modifier <u>css:</u> sucht nach Stylesheets. Die Filterung nach Kategorien wird direkt untersützt:
	 <i>css:layout:Navi</i> wechselt in die Kategrie Navi im Stylesheet layout.'),
    array('<u>f:</u> Function', 'Der Modifier <u>f:</u> listet Funktionen wie das Datenbankupdate auf.'),
    array('<u>new:</u> Neues Element', 'Der Modifier <u>new:</u> sucht nach Themes. Als zweite Verfollständigung kann Modul, Stylesheet oder das Seitenlayout gewählt werden um eines dieser Elemente zu erstellen.'),

);

