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
	'LOG_RSP_HANDEL'				=> '<strong>Handel</strong><br />» Es wurden %1$s %2$s mit der Nachricht:<br /> "%3$s" <br /> verschickt.',
	'LOG_RSP_NEUES_UNTERNEHMEN'		=> '<strong>Neues Unternehmen</strong><br />» %1$s',
	'LOG_RSP_DELETE_UNTERNEHMEN'	=> '<strong>Unternehmen gelöscht</strong><br />» %1$s',
	'LOG_RSP_EDIT_UNTERNEHMEN'		=> '<strong>Unternehmen geändert</strong><br />» Aus %1$s wurde %2$s',
	'LOG_RSP_NEUER_BETRIEB'			=> '<strong>Neuer Betrieb</strong><br />» "%1$s" im Unternehmen "%2$s" in der Provinz "%3$s"',
	'LOG_RSP_DELETE_BETRIEB'		=> '<strong>Betrieb gelöscht</strong><br />» "%1$s" im Unternehmen "%2$s" in der Provinz "%3$s"',
	'LOG_RSP_RESS_ERSTELLT'			=> '<strong>Ware erstellt</strong><br />» %1$s %2$s des Unternehmens "%3$s" in der Provinz "%4$s" ',
	'LOG_RSP_RANG'					=> '<strong>Rang</strong><br />» Wurde zum %1s befördert.',
	'LOG_RSP_NO_RANG'				=> '<strong>Rang</strong><br />» Hat keinen Rang mehr.',
	'LOG_RSP_RIBBEN'				=> '<strong>Orden</strong><br />» Hat %1s verliehen bekommen.',
	'LOG_CLEAR_RSP_RANG'			=> 'Eintrag im RSP-Protokoll gelöscht.',
	'LOG_RSP_AMT'					=> '<strong>AMT</strong><br />» Dient nun als %1s.',
	'LOG_RSP_NO_AMT'				=> '<strong>AMT</strong><br />» Wurde seines Amtes enthoben.',
    'LOG_RSP_AUSBAU_BETRIEB'        => '<strong>Betrieb erweitert</strong><br />» Das Gebäude %1$s im Unternehmen %2$s wurde um eine Stufe erweitert.',
    'RSP_LOG_NEUER_BETRIEB'         => '<strong>Neuer Betrieb</strong><br />» "%1$s" im Unternehmen "<a href="%2$s">%3$s</a>" in der Provinz "<a href="%4$s">%5$s</a>"',
    'RSP_LOG_AUSBAU_BETRIEB'        => '<strong>Betrieb erweitert</strong><br />» In der Provinz %1$s wurde das Gebäude %1$s vom Unternehmen %2$s um eine Stufe erweitert.',
    'RSP_LOG_HANDEL'		        => '<strong>Handel (%1$s)</strong><br />» %2$s hat %3$s %4$s mit der Nachricht:<br /> "%5$s" <br />an %6$s verschickt.',
    'RSP_LOG_HAENDLER'		        => '<strong>Handel (%1$s)</strong><br />» Wir haben für %2$s %3$s vom %4$s folgendes bekommen: %5$s %6$s.',
    'RSP_AKTIV'                     => 'Unterwegs',
    'RSP_BEENDET'                   => 'Beendet',
));
?>