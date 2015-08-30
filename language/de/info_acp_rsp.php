<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_RSP'						=> 'RSP',
	
	'RSP_AMT'						=> 'Amt',
	
	'RSP_BEREICH_NON'				=> 'Kein Amt',
	'RSP_BEREICH_GOV'				=> 'Provinzgouverneur',
	'RSP_BEREICH_PRA'				=> 'Staatspräsident',
	'RSP_BEREICH_SEK'				=> 'Staatssekretär',
	'RSP_BEREICH_MIN'				=> 'Minister',
	
	'RSP_CHANGELOG'					=> 'Changelog',
	'RSP_CHANGELOG_ADD'				=> 'Changelog anlegen',
	'RSP_CHANGELOG_DRAFT'			=> 'Changelog schreiben',
	'RSP_CHANGELOG_DRAFT_EXPLAIN'	=> 'Schreibe hier eine neuen Changelog Eintrag',
	'RSP_CHANGELOG_DRAFT_PREVIEW'	=> 'Changelog Vorschau',
	'RSP_CHANGELOG_ENTRY_ADDED'		=> 'Changelog Eintrag erstellt.',
	'RSP_CHANGELOG_ENTRY_DELETED'	=> 'Changelog Eintrag gelöscht.',
	'RSP_CHANGELOG_MANAGE'			=> 'Changelog verwalten',
	'RSP_CHANGELOG_MANAGE_EXPLAIN'	=> 'Hier kannst du Changelog-Einträge löschen.',
	'RSP_CREATE_USER'				=> 'RSP-Account erstellen',
	'RSP_CREATE_USER_SUCCESS'		=> 'RSP-Nutzer erfolgreich aktiviert.',
	
	'RSP_DELETE_USER'				=> 'RSP-Account löschen',
	'RSP_DELETE_USER_SUCCESS'		=> 'RSP-Nutzer erfolgreich entfernt.',

	'RSP_LAND'						=> 'Land',
	'RSP_LAND_FRT'					=> 'FRT',
	'RSP_LAND_USR'					=> 'USR',
	'RSP_LAND_VRB'					=> 'VRB',
	
	'RSP_MANAGE_USER_SUCCESS'		=> 'RSP-Benutzerdetails geändert',
	'RSP_MANAGE_RANK_SUCCESS'		=> 'RSP-Rang eines Spielers geändert',
	
	'RSP_RANG'						=> 'Rang',
	'RSP_RANK'						=> 'Benutzer Rang',
	'RSP_RANK_CURRENT'				=> 'Aktueller Rang',
	'RSP_RESS_ADD'					=> 'Hinzufügen',
	'RSP_RESS_ART'					=> 'Ressource',
	'RSP_RESS_MODUS'				=> 'Aktion',
	'RSP_RESS_MENGE'				=> 'Menge',
	'RSP_RESS_SUB'					=> 'Abziehen',
	'RSP_RESS_TEXT'					=> 'Text',
	
	'RSP_TITLE_CHANGELOG'			=> 'RSP-Changelog verwalten',
	'RSP_TITLE_TRADING'				=> 'RSP-Preise anpassen',
	'RSP_TITLE_USER'				=> 'RSP-Benutzerdetails bearbeiten',
	'RSP_TRADER'					=> 'Händler',
	'RSP_TRADING'					=> 'Handel',
	'RSP_TRADER_UPDATED'			=> 'Preis aktualisiert',
	'RSP_TRADING_ADMIN_EXPLAIN'		=> 'Hier kannst du die Preise für die Rohstoffe einstellen.',
	
	'RSP_USER'						=> 'Benutzer',
	'RSP_USERNAME'					=> 'RSP-Name',
	'RSP_USER_ADMIN'				=> 'RSP-Benutzer verwalten ',
	'RSP_USER_ADMIN_EXPLAIN'		=> 'Hier kannst du die Benutzer im RSP-Bereich verwalten.',
	'RSP_USER_EDITED'				=> 'RSP-Benutzerdetails geändert',
	'RSP_USER_RANK'					=> 'RSP-Benutzer Rang',
	'RSP_USER_RESS_OVERVIEW'		=> 'Übersicht über die Ressourcen des Spielers',
	
	'SELECT_USER'					=> 'Wähle Benutzer',
	
	'RSP_WIRTSCHAFT'				=> 'Wirtschaft',
));