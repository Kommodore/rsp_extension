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
/**
* @package acp
*/
class acp_rsp
{
	var $u_action;
	function main($id, $mode)
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		
		$username	= utf8_normalize_nfc(request_var('username', '', true));
		$user_id	= request_var('u', 0);
		
		$user->add_lang('mods/rsp_acp');
		$this->tpl_name = 'acp_rsp_wirtschaft';
		add_form_key('acp_rsp_wirtschaft');
		
		// Wähle den Benutzer aus
		if (!$username && !$user_id)
		{
			$template->assign_vars(array(
				'U_ACTION'			=> $this->u_action,

				'S_RSP_SELECT_USER'		=> true,
				'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=select_user&amp;field=username&amp;select_single=true'),
			));

			return;
		}
		
		if (!$user_id)
		{
			$sql = 'SELECT user_id
				FROM ' . USERS_TABLE . "
				WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";
			$result = $db->sql_query($sql);
			$user_id = (int) $db->sql_fetchfield('user_id');
			$db->sql_freeresult($result);

			if (!$user_id)
			{
				trigger_error($user->lang['NO_USER'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}	
		
		switch ($mode)
		{
			case 'rank':
				$title = 'ACP_RSP_USER_RANK';
				$this->user_rank($user_id,$username);
			break;

			default:
				$title = 'ACP_RSP_USER';
				$this->rsp_user($user_id,$username);
			break;
		}
		$this->page_title = $user->lang[$title];
	}
}

?>