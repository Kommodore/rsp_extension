<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\acp;

class rsp_user_module
{
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container, $request, $user, $db;
		
		

		// Get an instance of the admin controller and set the template (we only need acp_rsp_user in this file)
		$admin_controller = $phpbb_container->get('tacitus89.rsp.admin.controller');
		$this->tpl_name = 'acp_rsp_user';
		
		// Requests
		$mode = $request->variable('mode', '');
		$user_id = $request->variable('u',0);
		$username = $request->variable('username','');
	
		// If there isn't a username and user ID show user selection
		if($user_id==0 && $username=='')
		{
			$admin_controller->user_selection($user_id);
			
			// Return to stop execution of this script
			return;
		}
		
		// if there isn't a user ID select it from the database
		if($user_id==0 && $username!='')
		{
			$sql = 'SELECT user_id FROM '. USERS_TABLE .' WHERE username_clean = "'. strtolower($db->sql_escape($username)) .'"';
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			$user_id = $row['user_id'];
		}
		
		// If there is still no user ID the user isn't existing
		if($user_id == 0)
		{
				trigger_error($user->lang('NO_USER') . adm_back_link($this->u_action), E_USER_WARNING);
		}
		
		// Load the module modes
		switch ($mode)
		{
			case 'rank':
				
				// If user submitted the form update the rank
				if($request->is_set_post('update_rank'))
				{
					$admin_controller->update_rsp_rank($user_id);
				}
				$admin_controller->rank_overview($user_id);
				
				// Return to stop execution of this script 
 				return; 
			break;
			
			case 'wirtschaft':
			
				// If user submitted the form check if he wants to add or substract a resource
				if($request->is_set_post('update_ress'))
				{
					if($request->variable('rsp_ress_modus','') == 'add')
					{
						$admin_controller->wirtschaft_add_ress($user_id);
					}
					if($request->variable('rsp_ress_modus','') == 'sub')
					{
						$admin_controller->wirtschaft_delete_ress($user_id);
					}
				}
				$admin_controller->wirtschaft_overview($user_id);
				
				// Return to stop execution of this script 
 				return; 
			break;
			
			case 'user':
			default:
			
				// Execute if user is in rsp and administrator wants to update country or rsp name
				if($request->is_set_post('rsp_overview'))
				{
					$admin_controller->update_rsp_user($user_id);
				}
				if($request->is_set_post('rsp_manage'))
				{
					//Execute if submit and checkbox for deleting were used
					if($request->is_set('delete_rsp_user'))
					{
						$admin_controller->delete_rsp_user($user_id);
					}
					
					//Execute if submit and checkbox for creating were used
					if($request->is_set('create_rsp_user'))
					{
						$admin_controller->add_rsp_user($user_id);
					}
				}
				$admin_controller->user_overview($user_id);
				
				// Return to stop execution of this script 
 				return; 
			break;
		}
		$this->page_title = $user->lang['RSP_TITLE_USER'];
	}
}