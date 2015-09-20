<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \tacitus89\rsp\Entity\changelog;

/**
* Admin controller
*/
class admin_controller implements admin_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\pagination */
	protected $pagination;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var \phpbb\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	public $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $container;

	/** @var string phpBB root path */
	protected $root_path;
	
	/** @var string phpBB root path */
	protected $db_prefix;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\config\config                 $config          Config object
	* @param \phpbb\controller\helper			  $helper          Controller helper object
	* @param \phpbb\pagination					  $pagination	   Pagination object
	* @param \phpbb\log                           $log             Log object
	* @param \phpbb\db\driver\driver_interface	  $db			   Database Object
	* @param \phpbb\request\request               $request         Request object
	* @param \phpbb\template\template             $template        Template object
	* @param \phpbb\user                          $user            User object
	* @param ContainerInterface                   $container       Service container interface
	* @param string                               $root_path       phpBB root path
	* @param string                               $db_prefix       Database prefix
	* @param string                               $php_ext         phpEx
	* @return \tacitus89\rsp_extension\controller\admin_controller
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\pagination $pagination, \phpbb\log\log $log, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, ContainerInterface $container, $root_path, $db_prefix, $php_ext){
		$this->config = $config;
		$this->helper = $helper;
		$this->pagination = $pagination;
		$this->log = $log;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->container = $container;
		$this->root_path = $root_path;
		$this->db_prefix = $db_prefix;
		$this->php_ext = $php_ext;
		$this->u_action = $u_action;
		$this->ext_path = 'ext/tacitus89/rsp/';
	}
	
	/**
	* Display user selection
	*
	* @return null
	* @access public
	*/
	public function user_selection()
	{
		$this->tpl_name = 'acp_rsp_user';
		$this->template->assign_vars(array(
			'S_MODE' 			=>	$mode,
			'S_RSP_SELECT_USER' => true,
		));
	}
	
	/**
	* get the overview of a selected user
	*
	* @param int user_id The ID of the user to be edited
	* @return null
	* @access public
	*/
	public function user_overview($user_id)
	{
		// Get RSP Userdata
		$sql = 'SELECT username, user_rsp, user_rsp_land_id, user_rsp_name FROM '. USERS_TABLE .' WHERE user_id = '. (int) $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		// Pass variables to the teamplate
		$this->template->assign_vars(array(
			'MANAGED_USERNAME' 		=> $row['username'],
			'S_USER_IN_RSP'			=> ($row['user_rsp']) ? true : false,
			'S_USER_NOT_IN_RSP'		=> (!$row['user_rsp']) ? true : false,
			'U_RSP_USER_LAND_FRT'	=> ($row['user_rsp_land_id']==1) ? true : false,
			'U_RSP_USER_LAND_USR'	=> ($row['user_rsp_land_id']==2) ? true : false,
			'U_RSP_USER_LAND_VRB'	=> ($row['user_rsp_land_id']==3) ? true : false,
			'U_RSP_USERNAME'		=> $row['user_rsp_name'],
			'S_RSP_USER_OVERVIEW' 	=> true,
			'U_ACTION'				=> build_url() .'&amp;u='.$user_id
		));
	}
	
	/**
	* Display all ranks and the rank of the selected user
	*
	* Display the rank and office of a selected user
	* @param int user_id the ID of the user to be edited
	* @return null
	* @access public
	*/
	public function rank_overview($user_id){
		
		// Load user details
		$sql = 'SELECT r.url, r2.beruf as amt ,u.user_rsp_rang, u.user_rsp_amt, u.username
				FROM ' . USERS_TABLE . ' u
				LEFT JOIN '. $this->db_prefix .'rsp_raenge r ON r.id = u.user_rsp_rang
				LEFT JOIN '. $this->db_prefix .'rsp_raenge r2 ON r2.id = u.user_rsp_amt
				WHERE u.user_id = "'. (int) $user_id .'"';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		
		// Pass the first set of variables to the teamplate
		$this->template->assign_vars(array(
			'MANAGED_USERNAME' 		=> $row['username'],
			'U_RSP_USER_AMT_NON'	=> ($row['amt'] == 0) ? true:false,
			'U_RSP_USER_AMT_GOV'	=> ($row['amt'] == 'GOV')? true:false,
			'U_RSP_USER_AMT_PRA'	=> ($row['amt'] == 'PRA')? true:false,
			'U_RSP_USER_AMT_SEK'	=> ($row['amt'] == 'SEK')? true:false,
			'U_RSP_USER_AMT_MIN'	=> ($row['amt'] == 'MIN')? true:false,
			'U_RSP_USER_RANK_BILD'	=> $this->root_path.$this->ext_path.$row['url'],
			'S_RSP_USER_RANK' 		=> true,
			'U_ACTION'				=> build_url() .'&amp;u='.$user_id
		));
		
		// Load all possible ranks
		$sql = 'SELECT r.id, r.name, r.beruf, r.stufe, r.url, l.id AS land_id, l.name AS landname, u.user_rsp_rang
			FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['land'] .' l
			LEFT JOIN '. $this->db_prefix.\tacitus89\rsp\tables::$table['rsp_raenge'] .' r ON l.id = r.land
			LEFT JOIN ' . USERS_TABLE . ' u ON u.user_id = "'. $row['user_id'] .'"
			ORDER BY l.id, r.beruf, r.stufe';
		$result = $this->db->sql_query($sql);
		$land = -1;
		
		// Option to delete rank
		$this->template->assign_block_vars('user_rang', array(
			'ID'		=> -2,
			'NAME'		=> '&nbsp;&nbsp;&nbsp;>>> Rang entfernen <<<',
		));
		
		// Pass all variables to the template
		while($row = $this->db->sql_fetchrow($result))
		{
			if($land != $row['land_id'])
			{
				$this->template->assign_block_vars('user_rang', array(
					'ID'		=> -1,
					'NAME'		=> $row['landname'],
				));
				
				$land = $row['land_id'];
			}
			
			$this->template->assign_block_vars('user_rang', array(
				'ID'		=> $row['id'],
				'NAME'		=> '&nbsp;&nbsp;&nbsp;&nbsp;[' . $row['beruf'] ." - ".  $row['stufe'] . "] " . $row['name'],
				'OPTION'	=> ($row['id'] == $row['user_rsp_rang'])? 'selected="selected"': "",
			));
		}
		$this->db->sql_freeresult($result);
	}
	
	/**
	* Edit the wirtschaft of a user
	*
	* @param int user_id The ID of the user where the wirtschaft should be edited
	* @return null
	* @access public
	*/
	public function wirtschaft_overview($user_id)
	{
		
		// Get RSP Userdata
		$sql = 'SELECT user_rsp, username FROM '. USERS_TABLE .' WHERE user_id = '. (int) $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		// Pass the first set of variables to the template
		$this->template->assign_vars(array(
			'S_RSP_WIRTSCHAFT' 	=> true,
			'S_USER_IN_RSP'		=> ($row['user_rsp']==1) ? true : false,
			'MANAGED_USERNAME' 	=> $row['username'],
			'U_ACTION'			=> build_url() .'&amp;u='.$user_id
		));
						
		// Create a list of all resources for the select menu
		$sql = 'SELECT id, name
			FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['ressourcen'] .'
			ORDER BY id ASC';
		$result = $this->db->sql_query($sql);
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('ress_block', array(
				'ID'		=> $row['id'],
				'NAME'		=> $row['name'],
			));
		}
		$this->db->sql_freeresult($result);
		
		//Create a list of all resources and how much of them the user owns
		$sql = 'SELECT r.name, u.menge
			FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['ressourcen'] .' r
			LEFT JOIN '. $this->db_prefix.\tacitus89\rsp\tables::$table['user_ress'] .' u ON u.ress_id = r.id
			WHERE u.user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('user_ress_block', array(
				'NAME'		=> $row['name'],
				'MENGE'		=> $row['menge'],
			));
		}
		$this->db->sql_freeresult($result);
	}
	
	/**
	* Allow a user to participate in the rsp
	*
	* @param int user_id The ID of the user to be created
	* @return null
	* @access public
	*/
	public function add_rsp_user($user_id)
	{
		
		// Get form data
		$rsp_username = $this->request->variable('rsp_username','',true);
		$rsp_land_id = $this->request->variable('rsp_land',0);
		
		// Update Database
		$sql_array = array(
			'user_rsp_name'     => $rsp_username,
			'user_rsp_land_id'  => $rsp_land_id,
			'user_rsp'      	=> 1
		);
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);
		
		// Insert zero as value of all user resources linked to the new user
		$sql = 'SELECT id as ress_id
			FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['ressourcen'] .'
			ORDER BY id ASC';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		for($i = 0; $i < count($row); $i++){
			$row[$i]['user_id'] = $user_id;
			$row[$i]['menge'] = 0;
		}
		$this->db->sql_multi_insert($this->db_prefix.\tacitus89\rsp\tables::$table['user_ress'], $row);
		
		//Log action & throw success message
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'RSP_USER_EDITED');
		trigger_error($this->user->lang('RSP_CREATE_USER_SUCCESS') . adm_back_link($this->u_action));
	}
	
	/**
	* Delete a user from the rsp
	*
	* @param int user_id The ID of the user to be deleted
	* @return null
	* @access public
	*/
	public function delete_rsp_user($user_id)
	{
		// Update Database
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET user_rsp_name = "", user_rsp_land_id = 0, user_rsp = 0
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);
		
		// Delete ALL resources the user owned
		$sql = 'DELETE FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['user_ress'] .' WHERE user_id = '. (int) $user_id;
		$this->db->sql_query($sql);
		
		//Log action and throw success message
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'RSP_USER_EDITED');
		trigger_error($this->user->lang('RSP_DELETE_USER_SUCCESS') . adm_back_link($this->u_action));
	}
	
	/**
	* Update the rsp details of a user
	*
	* @param int user_id The ID of the user to be updated
	* @return null
	* @access public
	*/
	public function update_rsp_user($user_id)
	{
		
		// Get form data
		$rsp_username = $this->request->variable('rsp_username','',true);
		$rsp_land_id = $this->request->variable('rsp_land','');
		
		//Update Database
		$sql_array = array(
			'user_rsp_name'     => $rsp_username,
			'user_rsp_land_id'  => $rsp_land_id,
		);
	
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);
		
		//Log action & throw success message
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'RSP_USER_EDITED');
		trigger_error($this->user->lang('RSP_MANAGE_USER_SUCCESS') . adm_back_link($this->u_action));
	}
	
	/**
	* Add a resource to a user
	*
	* @param int user_id The ID of the user where the wirtschaft should be edited
	* @return null
	* @access public
	*/
	public function wirtschaft_add_ress($user_id)
	{
		// Grab form data
		$rsp_ress_menge = $this->request->variable('rsp_ress_menge',0);
		$rsp_ress_art = $this->request->variable('rsp_ress_art',0);
		$rsp_ress_text = $this->request->variable('rsp_ress_text','');
		
		// Add resources
		$sql = 'UPDATE '. $this->db_prefix.\tacitus89\rsp\tables::$table['user_ress'] .'
			SET menge = menge+'.$rsp_ress_menge.'
			WHERE user_id = "'. $user_id .'" and
				ress_id = '. $rsp_ress_art;
		$this->db->sql_query($sql);
		
		// Create an entry in the handel log
		$sql = 'INSERT INTO '. $this->db_prefix.\tacitus89\rsp\tables::$table['handel_log'] .' ' . $this->db->sql_build_array('INSERT', array(
			'sender_id'	=> (int) 59,
			'empfaenger_id'	=> (int) $user_id,
			'zweck_text' => (string) htmlspecialchars_decode($rsp_ress_text),
			'ressource_art' => (int) $rsp_ress_art,
			'menge' => (int) $rsp_ress_menge,
			'time' => (int) time(),
			'status' => 1,
		));
		$this->db->sql_query($sql);
		
		redirect(build_url() .'&amp;u='.$user_id);
	}
	
	/**
	* Substract a resource from a user
	*
	* @param int user_id The ID of the user where the wirtschaft should be edited
	* @return null
	* @access public
	*/
	public function wirtschaft_delete_ress($user_id)
	{
		// Grab form data
		$rsp_ress_menge = $this->request->variable('rsp_ress_menge',0);
		$rsp_ress_art = $this->request->variable('rsp_ress_art',0);
		$rsp_ress_text = $this->request->variable('rsp_ress_text','');
		
		// Add resources
		$sql = 'UPDATE '. $this->db_prefix.\tacitus89\rsp\tables::$table['user_ress'] .'
			SET menge = menge-'.$rsp_ress_menge.'
			WHERE user_id = "'. $user_id .'" and
				ress_id = '. $rsp_ress_art;
		$this->db->sql_query($sql);
		
		// Create an entry in the handel log
		$sql = 'INSERT INTO '. $this->db_prefix.\tacitus89\rsp\tables::$table['handel_log'] .' ' . $this->db->sql_build_array('INSERT', array(
			'sender_id'	=> (int) 59,
			'empfaenger_id'	=> (int) $user_id,
			'zweck_text' => (string) htmlspecialchars_decode($rsp_ress_text),
			'ressource_art' => (int) $rsp_ress_art,
			'menge' => (int) $rsp_ress_menge,
			'time' => (int) time(),
			'status' => 1,
		));
		$this->db->sql_query($sql);
		
		redirect(build_url() .'&amp;u='.$user_id);
	}
	
	/**
	* Update the rank of a user
	*
	* @param int user_id The ID of the user to be edited
	* @return null
	* @access public
	*/
	public function update_rsp_rank($user_id){
		
		// Get form data
		$rsp_rang	= $this->request->variable('rsp_rang', 0);
		$rsp_amt	= $this->request->variable('rsp_amt', '');
		
		// Update user table with rank changes
		if($rsp_rang == -2)
		{
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_rsp_rang = 0
					WHERE user_id = "'. (int) $user_id .'"';
			$this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
		}
		else
		{				
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_rsp_rang = '. $rsp_rang .'
					WHERE user_id = "'. (int) $user_id .'"';
			$this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
		}
		
		// If user has an amt
		if($rsp_amt != 'NON')
		{
			// Select ID's from amt table
			$sql = 'SELECT id, name
				FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['rsp_raenge'] .'
				WHERE beruf = "' . $rsp_amt . '"
					AND stufe = 0
					AND land = 0';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			
			// Update user table
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_rsp_amt = '. $row['id'] .'
					WHERE user_id = "'. (int) $user_id .'"';
			$this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
		}
		elseif($rsp_amt == 'NON' && $row['user_rsp_amt'] != 0)
		{
			
			// Update user table
			$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_rsp_amt = 0
					WHERE user_id = "'. (int) $user_id .'"';
			$this->db->sql_query($sql);
		}
		trigger_error($this->user->lang('RSP_MANAGE_RANK_SUCCESS') . adm_back_link($this->u_action));
	}
	
	/*
	* Display current trader prices and allow to update them
	*
	* @return null
	* @access public
	*/
	public function trader_overview()
	{
		// Grab all resources (resources table) and their price (haendler table)
		$sql = 'SELECT r.id, r.name, t.preis
			FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['haendler'] .' t
			INNER JOIN '. $this->db_prefix.\tacitus89\rsp\tables::$table['ressourcen'] .' r ON t.ressource_id = r.id
			ORDER BY id ASC';
		$result = $this->db->sql_query($sql);
		
		// Pass the data to the template
		while($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('res_block', array(
				'ID'		=> $row['id'],
				'NAME'		=> $row['name'],
				'PRICE'		=> $row['preis'],
			));
		}
		$this->template->assign_vars(array(
			'U_BACK'			=> $this->u_action,
			'U_ACTION'			=> $this->u_action,
			
		));
		$this->db->sql_freeresult($result);
	}
	
	/**
	* Update trader
	*
	* @param int price The price of the resource
	* @param int res_id The ID of the resource
	* @return null
	* @access public
	*/
	public function trader_update($price,$res_id)
	{
		if($res_id!=0)
		{
			$sql = 'UPDATE '. $this->db_prefix.\tacitus89\rsp\tables::$table['haendler'] .'
					SET preis = '. (int) $price .'
					WHERE ressource_id = '. (int) $res_id;
			$this->db->sql_query($sql);
			trigger_error($this->user->lang['RSP_TRADER_UPDATED'] . adm_back_link($this->u_action));
		}
	}
	
	/**
	* Manage changelog entrys
	*
	* @return null
	* @access public
	*/
	public function changelog_overview()
	{
		
		// Grab the last 10 changelog entrys from the database
		$sql = 'SELECT id, time, text, text_uid, text_bitfield, text_options
		FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['changelog'] .'
		ORDER BY time DESC';
		$result = $this->db->sql_query_limit($sql, 10);
		//Pass them and the rest to the template
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('log_block', array(
				'TEXT'		=> generate_text_for_display($row['text'],$row['text_uid'], $row['text_bitfield'], $row['text_options']),
				'TIME'		=> $this->user->format_date($row['time'], false, true),
				'U_DELETE'	=> build_url() .'&amp;entry_id='.$row['id'].'&amp;action=delete',
			));
		}
		$this->db->sql_freeresult($result);
		$this->template->assign_vars(array(
			'S_RSP_MANAGE'		=> true,
			'U_ACTION'		=> $this->u_action,
		));
	}
	
	/**
	* Add an entry in the changelog
	*
	* @return null
	* @access public
	*/
	public function changelog_add_entry()
	{
		// Only continue if this site got called in phpb for security reasons
		if (!defined('IN_PHPBB'))
		{
			exit;
		}
		
		//Include posting functions
		include($this->root_path . 'includes/functions_posting.' . $this->php_ext);
		include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		$this->user->add_lang(array('posting'));
		add_form_key('rsp_changelog');
		generate_smilies('inline', '',1);
		display_custom_bbcodes();
		
		// Checking if form is valid
		if ($this->request->is_set_post('preview') || $this->request->is_set_post('submit'))
		{
			if (!check_form_key('rsp_changelog'))
			{
				trigger_error('FORM_INVALID');
			}
		}
		
		// If form is submitted
		if ($this->request->is_set_post('submit'))
		{	
			
			// Store form content in database
			$text = $this->request->variable('rsp_changelog_text', '', true);
			$this->container->get('tacitus89.rsp.entity.changelog')->set_text($text)->insert();		
			trigger_error($this->user->lang['RSP_CHANGELOG_ENTRY_ADDED'] . adm_back_link($this->u_action));
		}
		
		// If user wants a preview
		if ($this->request->is_set_post('preview'))
		{
			$changelog_preview = '';
			$changelog_preview = $this->changelog_preview($this->request->variable('rsp_changelog_text', '', true));
		}
		
		// Pass some variables to the template
		$this->template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
			
			'S_RSP_CREATE'	=> true,
			'S_RSP_PREVIEW'		=> ( $changelog_preview ) ? TRUE : FALSE,
			'CHANGELOG_TEXT'	=> ( $changelog_preview ) ? $this->request->variable('rsp_changelog_text', '', true) : '',
			'RSP_PREVIEW'		=> ( $changelog_preview ) ? $changelog_preview : '',
			)
		);
	}
	
	/**
	* Delete an entry from the changelog
	*
	* @param int entry_id The ID of the entry to be deleted
	* @return null
	* @access public
	*/
	public function changelog_delete($entry_id)
	{
		// Use a confirmation box routine when deleting an entry
		if(confirm_box(true)){
			$this->container->get('tacitus89.rsp.entity.changelog')->delete($entry_id);
			trigger_error($this->user->lang['RSP_CHANGELOG_ENTRY_DELETED'] . adm_back_link($this->u_action));
		}
		else
		{
			// Request confirmation from the user to delete the game
			confirm_box(false, $this->user->lang('ACP_CONFIRM_DELETE_ENTRY'), build_hidden_fields(array(
				'mode' => 'manage',
				'action' => 'delete',
				'entry_id' => $entry_id,
			)));

			// Use a redirect to take the user back to the previous page
			// if the user chose not delete the entry from the confirmation page.
			redirect(build_url()."&amp;action=manage");
		}
	}
	
	/**
	* Preview a new entry for the changelog
	*
	* @param string text The Text to be previewed
	* @return string
	* @access public
	*/
	function changelog_preview($text)
	{
		$uid			= $bitfield			= $options	= '';	
		$allow_bbcode	= $allow_smilies	= $allow_urls = true;
		
		//lets (mis)use generate_text_for_storage to create some uid, bitfield... for our preview
		generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
		
		// after creating it, display the preview to the user
		$text = generate_text_for_display($text, $uid, $bitfield, $options);
		
		return $text;
	}
}