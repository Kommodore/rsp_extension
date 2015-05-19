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
	'LOG_RSP_RANG'					=> '<strong>Rang</strong><br />» Wurde zum %1s befördert.',
	'LOG_RSP_AMT'					=> '<strong>AMT</strong><br />» Dient nun als %1s.',
	'LOG_RSP_RIBBEN'				=> '<strong>Orden</strong><br />» Hat %1s verliehen bekommen.',
	'LOG_CLEAR_RSP_RANG'			=> 'Eintrag im RSP-Rang-Protokoll gelöscht.',
	'LOG_RSP_NO_AMT'				=> 'Wurde seinem Amt enthoben.',
));
?>