<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\acp;

class rsp_trading_module
{
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container, $request, $user, $db;
		
		

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('tacitus89.rsp.admin.controller');
		
		// Requests (not needed in the current state)
		$mode = $request->variable('mode', '');

			// Load the module modes
			switch ($mode)
			{	
				case 'trader':
				default:
					$this->page_title = $user->lang['RSP_TITLE_TRADING'];
					$this->tpl_name = 'acp_rsp_trading';
					if($request->is_set_post('submit'))
					{
						$admin_controller->trader_update($request->variable('res_price',0),$request->variable('res_id',0));
					}
					else
					{
						$admin_controller->trader_overview();
					}
					// Return to stop execution of this script 
 					return; 
					
				break;
			}
		
	}
}