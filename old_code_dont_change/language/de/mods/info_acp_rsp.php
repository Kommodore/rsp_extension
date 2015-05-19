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
	'ACP_RSP_USER'			=> 'Benutzer',
	'ACP_RSP_USER_RANK'		=> 'Benutzer Rang',
	
	'ACP_RSP_LOGS'				=> 'RSP-Protokoll',
	'ACP_RSP_LOGS_EXPLAIN'		=> 'Diese Liste zeigt alle Vorgänge, die im RSP-Bereich durchgeführt wurden.',
	
	'ACP_RSP_RANG_LOGS'				=> 'RSP-Rang-Protokoll',
	'ACP_RSP_RANG_LOGS_EXPLAIN'		=> 'Diese Liste zeigt alle Vorgänge im Bereich Ränge und Orden, die im RSP-Bereich durchgeführt wurden.',
	
	'ACP_RSP_HAENDLER'		=> 'Händler',
	'ACP_RSP_WIRTSCHAFT'	=> 'Wirtschaft',
	'USER_ADMIN_EXPLAIN'	=> '',
	'RSP_HAENDLER'			=> 'Händler',
	'RSP_WIRTSCHAFT'		=> 'Wirtschaft',
));
?>