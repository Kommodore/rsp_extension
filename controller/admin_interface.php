<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\controller;


/**
* Interface for our admin controller
*
* This describes all of the methods we'll use for the admin front-end of this extension
*/
interface admin_interface
{
	/**
	* Display user selection
	*
	* @return null
	* @access public
	*/
	public function user_selection();
	
	/**
	* Get the overview of a selected user
	*
	* @param int user_id The ID of the user to be edited
	* @return null
	* @access public
	*/
	public function user_overview($user_id);

	/**
	* Edit the rank of a user
	*
	* Display the rank and office of a selected user
	* @return null
	* @access public
	*/
	public function rank_overview($user_id);
	
	/**
	* Edit the wirtschaft of a user
	*
	* @param int user_id The ID of the user where the wirtschaft should be edited
	* @return null
	* @access public
	*/
	public function wirtschaft_overview($user_id);
	
	/**
	* Display trader
	*
	* Determine the price of the resources
	* @return null
	* @access public
	*/
	public function trader_overview();
	
	/**
	* Update trader
	*
	* @param int price The price of the resource
	* @param int res_id The ID of the resource
	* @return null
	* @access public
	*/
	public function trader_update($price,$res_id);
	
	/**
	* Manage changelog entrys
	*
	* @return null
	* @access public
	*/
	public function changelog_overview();
	
	/**
	* Allow a user to participate in the RSP
	*
	* @param int user_id The ID of the user to be created
	* @return null
	* @access public
	*/
	public function add_rsp_user($user_id);
	
	/**
	* Delete a user from the rsp
	*
	* @param int user_id The ID of the user to be deleted
	* @return null
	* @access public
	*/
	public function delete_rsp_user($user_id);
	
	/**
	* Update the rsp details of a user
	*
	* @param int user_id The ID of the user to be updated
	* @return null
	* @access public
	*/
	public function update_rsp_user($user_id);
	
	/**
	* Add a resource to a user
	*
	* @param int user_id The ID of the user where the wirtschaft should be edited
	* @return null
	* @access public
	*/
	public function wirtschaft_add_ress($user_id);
	
	/**
	* Substract a resource from a user
	*
	* @param int user_id The ID of the user where the wirtschaft should be edited
	* @return null
	* @access public
	*/
	public function wirtschaft_delete_ress($user_id);
	
	/**
	* Update the rank of a user
	*
	* @return null
	* @access public
	*/
	public function update_rsp_rank($user_id);
	
	/**
	* Add an entry in the changelog
	*
	* @return null
	* @access public
	*/
	public function changelog_add_entry();
	
	/**
	* Delete an entry from the changelog
	*
	* @param int entry_id The ID of the entry to be deleted
	* @return null
	* @access public
	*/
	public function changelog_delete($entry_id);
	
	/**
	* Preview a new entry for the changelog
	*
	* @param string text The Text to be previewed
	* @return null
	* @access public
	*/
	function changelog_preview($text);
}