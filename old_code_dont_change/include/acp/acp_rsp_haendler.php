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
class acp_rsp_haendler
{
	var $u_action;
	function main($id, $mode)
	{
		global $db, $template;
		global $phpbb_root_path, $phpEx;
		
		$title = 'ACP_RSP_HAENDLER';
		$this->tpl_name = 'acp_rsp_haendler';
		
		$submit 	= (isset($_POST['submit'])) ? true : false;
		
		$sql = 'SELECT b.id, a.preis, b.name
			FROM ' . RSP_HAENDLER_TABLE .' a
			INNER JOIN '. RSP_RESSOURCEN_TABLE .' b ON a.ressource_id = b.id
			ORDER BY id ASC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('ress_block', array(
				'ID'		=> $row['id'],
				'NAME'		=> $row['name'],
				'PREIS'		=> $row['preis'],
			));
		}
		
		$db->sql_freeresult($result);
		
		
		$template->assign_vars(array(
			'U_BACK'			=> $this->u_action,
			'U_ACTION'			=> $this->u_action,
			'S_FORM_OPTIONS'	=> $s_form_options,
		));
		
		if ($submit)
		{						
			$ress_id	= request_var('ress_id', 0);
			$ress_preis = request_var('ress_preis', 0);
			
			$sql = 'UPDATE ' . RSP_HAENDLER_TABLE . '
					SET preis = '. $ress_preis .'
					WHERE ressource_id = '. $ress_id;
			$db->sql_query($sql);
			trigger_error($user->lang['Updated'] . adm_back_link($this->u_action));
		}
		$this->page_title = $user->lang[$title];
	}
}

?>