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

function rsp_memberlist_viewprofile($user_id)
{
	global $db, $template, $user;
	global $phpbb_root_path, $phpEx;
	
	$user->add_lang('mods/rsp_uebersicht');
	
	/*foreach (glob("includes/rsp/*_class." . $phpEx) as $filename)
	{
	    require_once $filename;
	}*/

    require_once 'includes/rsp/benutzerunternehmen2_class.php';
    require_once 'includes/rsp/unternehmen2_class.php';
    require_once 'includes/rsp/betrieb2_class.php';
	
	$benutzerUnternehmen = new BenutzerUnternehmen2($user_id);
	$benutzerUnternehmen->listeUnternehmen();
	
}
