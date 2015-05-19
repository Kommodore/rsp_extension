<?php
/** 
*
* @package rsp
* @version $Id: acp_rsp.php 278 2012-10-22 19:18:26Z Strategie-Zone.de  $ 
* @copyright (c) 2011 Strategie-Zone.de 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}
// Merge language entries into the common lang array
$lang = array_merge($lang, array(
	'RSP_UNTERNEHMEN'		=> 'Unternehmen',
	'RSP_BETRIEB'			=> 'Betriebe',
	'RSP_BETRIEB_ROHSTOFFE'	=> 'Benötigte Rohstoffe',
	'USER_UNTERNEHMEN'		=> 'Deine Unternehmen',
	'PRODUZIEREN'			=> 'Produzieren',
	'LOESCHEN'				=> 'Löschen',
	'UNTERNEHMEN_LOESCHEN'	=> 'Unternehmen löschen',
	'UNTERNEHMEN_ERSTELLEN'	=> 'Unternehmen erstellen',
	'UNTERNEHMEN_NAME'		=> 'Unternehmenname: ',
	'WAEHLE_GUETERBEREICH'	=> 'Wähle einen Güterbereich: ',
	'ERSTELLEN'				=> 'Erstellen',
	'BETRIEB_BAUEN'			=> 'Betrieb bauen',
	'WAEHLE_BETRIEB'		=> 'Wähle einen Betrieb',
	'WAEHLE_PROVINZ'		=> 'Wähle eine Provinz',
	'BAUEN'					=> 'Bauen',
	'ROHSTOFFE_FEHLEN'		=> 'Ihnen fehlen die nötigen Rohstoffe!',
	'PRODUKTION'			=> 'Produktion:',
    'GEBAUDE_VERWALTEN'     => 'Gebäude verwalten',
    'UNTERNEHMEN_VERWALTEN' => 'Unternehmen verwalten',
    'AUSBAU'                => 'Ausbau',
    'AUSBAUEN'              => 'Ausbauen',
    'BETRIEBE'              => 'Betriebe',
    'BETRIEBNAME'           => 'Betriebname',
    'BETRIEBKOSTEN'         => 'Baukosten',
    'PROVINZEN'             => 'Provinz',
    'AKTION'                => 'Aktion',
    'STUFE'                 => 'Stufe',
    'BETRIEBE_LOESCHEN'     => 'Betriebe abreißen',
    'BETRIEB_LOESCHEN'      => 'Betrieb abreißen',
    'GEBAUDE_LOESCHEN'      => 'Gebäude abreißen',
    'AENDERN'               => 'Ändern',
));
?>