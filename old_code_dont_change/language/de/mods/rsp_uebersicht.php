<?php
/** 
*
* @package acp
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
	'RSP'						=> 'WiSim',
	//Handel
	'RSP_HANDEL'				=> 'Handel',
	'RSP_RESSOURCEN'			=> 'Ressourcen',
	'RSP_RESS_VERSCHICKEN'		=> 'Ressourcen verschicken',
	'RSP_BENUTZERNAME'			=> 'Benutzername',
	'RSP_VERWENDUNGSZWECK'		=> 'Verwendungszweck',
	'RSP_RESSOURCE'				=> 'Ressource',
	'RSP_MENGE'					=> 'Menge',
	'RSP_HAENDLER'				=> 'Weltmarkt',
	'RSP_LOG_NAME_SENDER'		=> 'Absender',
	'RSP_LOG_NAME_EMPFAENGER'	=> 'Empfänger',
	'RSP_LOG_ZWECK'				=> 'Zweck',
	'RSP_LOG_RESSOURCE'			=> 'Ressource',
	'RSP_LOG_MENGE'				=> 'Menge',
	'RSP_LOG_TIME'				=> 'Zeit',
	'RSP_LOG_STATUS'			=> 'Status',
	'ZUWENIG_ANGABEN'			=> 'Es fehlen Angaben!',
	
	'YOUR_UNTERNEHMEN'			=> 'Deine Unternehmen',
	'USER_UNTERNEHMEN'			=> 'Unternehmen des Benutzer',
	'RSP_UNTERNEHMEN'			=> 'Unternehmen',
	'RSP_BETRIEB'				=> 'Betriebe',
	
	'RESS_BEREICH_1'			=> 'Rohstoffe',
	'RESS_BEREICH_2'			=> 'Einf. Güter',
	'RESS_BEREICH_3'			=> 'Fort. Güter',
	'RESS_BEREICH_4'			=> 'Hochw. Güter',
	'RESS_BEREICH_5'			=> 'Kompl. Güter',
	
	'UNTERNEHMEN'				=> 'Unternehmen',
	'UNTERNEHMEN_AENDERN'		=> 'Unternehmen ändern',
	'UNTERNEHMEN_ERSTELLEN'		=> 'Unternehmen erstellen',
	'BETRIEB_BAUEN'				=> 'Betrieb bauen',
	'PROVINZ'					=> 'Provinz',
	'LAND'						=> 'Land',

    'RSP_RANKING'               => 'Ranking',
    'RSP_UBERSICHT'             => 'Übersicht',
    'RSP_MAP'                   => 'Wirtschaftskarte der Kaukasusregion',

    'RSP_LOG_DATUM'             => 'Datum',
    'RSP_LOG_TEXT'              => 'Meldung',
));
?>