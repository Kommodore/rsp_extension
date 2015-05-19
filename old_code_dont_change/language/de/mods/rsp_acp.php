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
	'SELECT_USER'			=> 'Wähle Benutzer',
	'ACP_RSP_USER'			=> 'RSP-Benutzer verwalten',
	'USER_ADMIN'			=> 'RSP-Benutzer verwalten',
	'USER_ADMIN_EXPLAIN'	=> 'Hier kannst du die Benutzer im RSP-Bereich verwalten.',
	'USER_OVERVIEW'			=> 'Benutzer Übersicht',
	'DELETE_RSP_USER'		=> 'RSP-Benutzer löschen',
	'CREATE_RSP_USER'		=> 'RSP-Benutzer anlegen',
	
	//User-Rank
	'ACP_RSP_USER_RANK'		=> 'RSP Benutzer Rang',
	'RSP_USER_RANK'			=> 'RSP-Benutzer Rang',
	'AKTUELLER_RANK'		=> 'Aktueller Rang',
	'RSP_LAND'				=> 'Land',
	'RSP_LAND_FRT'			=> 'FRT',
	'RSP_LAND_USR'			=> 'USR',
	'RSP_LAND_VRB'			=> 'VRB',
	'RSP_LAND_SPIELLEITER'	=> 'Mitarbeiter',
	'RSP_LAND_BEOBACHTER'	=> 'Beobachter',
	'RSP_BEREICH'			=> 'Bereich',
	'RSP_BEREICH_MIL'		=> 'Militär',
	'RSP_BEREICH_POL'		=> 'Polizei',
	'RSP_BEREICH_ZIV'		=> 'Zivilbürger',
	'RSP_BEREICH_REG'		=> 'Regierung',
	'RSP_RANG_STUFE'		=> 'Rang Stufe',
	
	'RSP_WIRTSCHAFT'		=> 'Wirtschaft',
	'RSP_RESS_ART'			=> 'Ressource',
	'RSP_RESS_MODUS'		=> 'Änderungsart :',
	'RSP_RESS_ADD'			=> 'Hinzufügen',
	'RSP_RESS_SUB'			=> 'Abziehen',
	'RSP_RESS_MENGE'		=> 'Menge :',
	'RSP_RESS_TEXT'			=> 'Text :',
	'USER_RESS_OVERVIEW'	=> 'Übersicht über die Ressourcen des Benutzer',
	
	'RSP_AMT'				=> 'AMT',
	'RSP_BEREICH_NON'		=> 'Kein Amt',
	'RSP_BEREICH_GOV'		=> 'Provinzgouverneur',
	'RSP_BEREICH_PRA'		=> 'Staatspräsident',
	'RSP_BEREICH_SEK'		=> 'Staatssekretär',
	'RSP_BEREICH_MIN'		=> 'Minister',
));
?>