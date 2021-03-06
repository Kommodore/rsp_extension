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
	'ACP_RSP_CHANGELOG_CREATE'		=> 'Changelog erstellen',
	'ACP_RSP_CHANGELOG_MANAGE'		=> 'Changelog verwalten',
	'CHANGELOG_TITLE'				=> 'Changelog',
	'CHANGELOG_MANAGE'				=> 'Changelog verwalten',
	'CHANGELOG_MANAGE_EXPLAIN'		=> 'Hier kannst du Changelog löschen.',
	'CHANGELOG_DRAFT_PREVIEW'		=> 'Changelog Vorschau',
	'CHANGELOG_DRAFT'				=> 'Changelog schreiben',
	'CHANGELOG_DRAFT_EXPLAIN'		=> 'Schreibe hier eine neuen Changelog Eintrag:',
	'RSP_UPDATE'					=> 'Aktualisiert',
));
?>