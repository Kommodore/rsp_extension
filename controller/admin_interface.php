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
	* @return nill
	* @access public
	*/
	public function user_selection();
	
	/**
	* Get the overview of a selected user
	*
	* @return null
	* @access public
	*/
	public function user_overview();

	/**
	* Edit the rank of a user
	*
	* Display the rank and office of a selected user
	* @return null
	* @access public
	*/
	public function rank_overview();
	
	/**
	* Edit the wirtschaft of a user
	*
	* @return null
	* @access public
	*/
	public function wirtschaft_overview();
	
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
	* @return null
	* @access public
	*/
	public function add_rsp_user();
	
	/**
	* Delete a user from the rsp
	*
	* @return null
	* @access public
	*/
	public function delete_rsp_user();
	
	/**
	* Update the rsp details of a user
	*
	* @return null
	* @access public
	*/
	public function update_rsp_user();
	
	/**
	* Update the rank of a user
	*
	* @return null
	* @access public
	*/
	public function update_rsp_rank();
	
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
