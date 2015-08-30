<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\acp;

class rsp_changelog_module
{
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container, $request, $user, $db;
		
		

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('tacitus89.rsp.admin.controller');
		
		// Requests
		$mode = $request->variable('mode', '');
		$action = $request->variable('action','');
		$entry_id = $request->variable('entry_id',0);

			// Load the module modes
			switch ($mode)
			{	
				case 'add':
					$this->page_title = $user->lang['RSP_TITLE_CHANGELOG'];
					$this->tpl_name = 'acp_rsp_changelog';
					$admin_controller->changelog_add_entry();
					
					// Return to stop execution of this script 
 					return; 

				break;
				
				case 'manage':
				default:
					switch($action)
					{
						case 'delete':
							$this->tpl_name = 'acp_rsp_changelog';
							$this->page_title = $user->lang['RSP_TITLE_CHANGELOG'];
							$admin_controller->changelog_delete($entry_id);
						break;
						default:
							$this->tpl_name = 'acp_rsp_changelog';
							$this->page_title = $user->lang['RSP_TITLE_CHANGELOG'];
							$admin_controller->changelog_overview();
						
						break;
					}
					// Return to stop execution of this script 
					return; 
				break;
			}
		
	}
}