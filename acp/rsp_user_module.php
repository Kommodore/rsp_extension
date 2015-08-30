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
		
		

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('tacitus89.rsp.admin.controller');
		
		// Requests
		$mode = $request->variable('mode', '');

			// Load the module modes
			switch ($mode){
				case 'rank':
					$this->tpl_name = 'acp_rsp_user';
					if(!isset($_POST['search_user']) && !isset($_POST['update_rank'])){
						$admin_controller-> user_selection();
					}
					else{
						$admin_controller->rank_overview();
					}
				break;
				
				case 'wirtschaft':
					$this->tpl_name = 'acp_rsp_user';
					if(!isset($_POST['search_user']) && !isset($_POST['ress_submit'])){
						$admin_controller-> user_selection();
					}
					else{
						$admin_controller->wirtschaft_overview();
					}
				break;
				
				case 'user':
				default:
					$this->tpl_name = 'acp_rsp_user';
					if(!isset($_POST['rsp_overview']) && !isset($_POST['rsp_manage']) && !isset($_POST['search_user'])){
						$admin_controller-> user_selection();
					}
					else{
						if(isset($_POST['rsp_manage'])){
							if(isset($_POST['create_rsp_user'])){
								$admin_controller->add_rsp_user();
							}
							if(isset($_POST['delete_rsp_user'])){
								$admin_controller->delete_rsp_user();
							}
						}
						if(isset($_POST['rsp_overview'])){
							$admin_controller->update_rsp_user();
						}
						$admin_controller->user_overview();
					}
				break;
			}
		
		$this->page_title = $user->lang['RSP_TITLE_USER'];
	}
}