<?php
/** 
*
* @package acp
* @version $Id: acp_rsp.php 278 2012-10-22 19:18:26Z Strategie-Zone.de  $ 
* @copyright (c) 2011 Strategie-Zone.de 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_rsp.' . $phpEx);

//ist es eine Json anfrage?
if(request_var('mode', '') == 'json')
{
	header('Content-Type: application/json; charset=utf-8"');
	jsonFunktion();
	return;
}


foreach (glob("includes/rsp_milsim/*." . $phpEx) as $filename)
{
    require_once $filename;
}


// Basic parameter data
$mode	= request_var('mode', '');
$action	= request_var('action', '');
$id		= request_var('i', 0);
$user_id= request_var('u', 0);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$user->add_lang('mods/rsp_milsim');

// Funktion zur �berpr�fung
// Ob User ein RSP-Spieler ist
if ($user->data['user_rsp'] == 0)
{
	redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
}

//RSP Index
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['RSP'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}milsim.$phpEx"))
);

switch ($mode)
{
    case 'rekrutierung':


    break;
	default:
        $template->set_filenames(array('body' => 'rsp_milsim_rekrutierung.html'));
        $rek = new Rekrutierung($user->data['user_id']);
        $rek->show();
	break;
}

$template->assign_vars(array(
	'L_CHANGELOG_TITLE'			=> $user->lang['L_CHANGELOG_TITLE'],
	'L_RSP_UPDATE'				=> $user->lang['L_RSP_UPDATE'],
	'U_FIND_USERNAME'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=rsp&amp;field=username&amp;select_single=true'),
));

page_header($user->lang['L_CHANGELOG_TITLE'], false);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>