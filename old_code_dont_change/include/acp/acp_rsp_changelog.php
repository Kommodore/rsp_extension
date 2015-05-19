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
class acp_rsp_changelog
{

	var $u_action;
	
	function main($id, $mode)
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
					
		switch ($mode)
		{
			case 'create':
				$title = 'ACP_RSP_CHANGELOG_CREATE';
				$this->create();
			break;

			default:
				$title = 'ACP_RSP_CHANGELOG_MANAGE';
				$this->manage();
			break;
		}
		
		$user->add_lang('mods/rsp_changelog');
		$this->page_title = $user->lang[$title];
		$this->tpl_name = 'acp_rsp_changelog';
		
		
	}
	
	function manage()
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		
		$log_id = request_var('id', 0);
		$delete_log	= utf8_normalize_nfc(request_var('action', '', true));
		
		if($log_id > 0 && $delete_log == 'delete')
		{
			$sql = 'DELETE FROM ' . RSP_CHANGELOG_TABLE . '
				WHERE id = ' . $log_id . '';
			$db->sql_query($sql);
			
			trigger_error($user->lang['Updated'] . adm_back_link($this->u_action));
		}
		
		$sql = 'SELECT id, time, text, text_uid, text_bitfield, text_options
		FROM ' . RSP_CHANGELOG_TABLE . "
		ORDER BY time DESC";
		$result = $db->sql_query_limit($sql, 10);
	
		while ($row = $db->sql_fetchrow($result))
		{
			$text = generate_text_for_display($row['text'],$row['text_uid'], $row['text_bitfield'], $row['text_options']);
			
			$url = $this->u_action . "&amp;id={$row['id']}";
			
			$template->assign_block_vars('log_block', array(
				'TEXT'		=> $text,
				'TIME'		=> $user->format_date($row['time'], false, true),
				'U_DELETE'	=> $url . '&amp;action=delete',
			));
		}	
		$db->sql_freeresult($result);
		
		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
			
			'S_RSP_MANAGE'	=> true,
			)
		);
	}
	
	function create()
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		
		include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
		
		$user->add_lang(array('posting'));
		add_form_key('rsp_changelog');
		
		// Smilies erstellen
		generate_smilies('inline', '',1);
		// Eigene BBCodes erstellen
		display_custom_bbcodes();
		
		// Variablen
		$preview	= (isset($_POST['preview'])) ? true : false;
		$submit 	= (isset($_POST['submit'])) ? true : false;
		$changelog_text	= utf8_normalize_nfc(request_var('rsp_changelog_text', '', true));
		
		//Alles ok?
		if ($submit || $preview)
		{
			if (!check_form_key('rsp_changelog'))
			{
				trigger_error('FORM_INVALID');
			}
		}
		
		if ($submit)
		{	
			$uid_text = $bitfield_text = $options_text = ''; // will be modified by generate_text_for_storage
			$allow_bbcode = $allow_urls = $allow_smilies = true;
			
			generate_text_for_storage($changelog_text, $uid_text, $bitfield_text, $options_text, $allow_bbcode, $allow_urls, $allow_smilies);
			
			// Neuen Changelog anlegen
			$sql = 'INSERT INTO ' . RSP_CHANGELOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'time'			=> time(),
				'text'			=> $changelog_text,
				'text_uid'		=> $uid_text,
				'text_bitfield'	=> $bitfield_text,
				'text_options'	=> $options_text,
				));
			$db->sql_query($sql);
			
			trigger_error($user->lang['Updated'] . adm_back_link($this->u_action));
		}
		
		$changelog_preview = '';
		if ($preview)
		{
			$changelog_preview = $this->preview_changelog($changelog_text);
		}
		
		//decode_message($changelog_text, '');
		
		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
			
			'S_RSP_CREATE'	=> true,
			'S_RSP_PREVIEW'		=> ( $changelog_preview ) ? TRUE : FALSE,
			'CHANGELOG_TEXT'	=> ( $changelog_preview ) ? $changelog_text : '',
			'RSP_PREVIEW'		=> ( $changelog_preview ) ? $changelog_preview : '',
			)
		);
	}
	
	function preview_changelog($text)
	{
		$uid			= $bitfield			= $options	= '';	
		$allow_bbcode	= $allow_smilies	= $allow_urls = true;
		//lets (mis)use generate_text_for_storage to create some uid, bitfield... for our preview
		generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
		//now we created it, lets show it
		$text			= generate_text_for_display($text, $uid, $bitfield, $options);
		
		return $text;
	}
}
?>