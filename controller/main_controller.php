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

/**
* Main controller
*/
class main_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $container;

	/** @var \tacitus89\rsp\operators\ressourcen */
	protected $ress_operator;

	/** @var \tacitus89\rsp\operators\unternehmen */
	protected $unternehmen_operator;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/** @var string Path to ext dir */
	protected $ext_path;

	/**
	* Constructor
	*
	* @param \phpbb\config\config                 $config          Config object
	* @param \phpbb\controller\helper			  $helper          Controller helper object
	* @param \phpbb\pagination					  $pagination	   Pagination object
	* @param \phpbb\request\request               $request         Request object
	* @param \phpbb\template\template             $template        Template object
	* @param \phpbb\user                          $user            User object
	* @param ContainerInterface                   $container       Service container interface
	* @param string                               $root_path       phpBB root path
	* @param string                               $php_ext         phpEx
	* @return \tacitus89\rsp_extension\controller\admin_controller
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\pagination $pagination, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, ContainerInterface $container, \tacitus89\rsp\operators\ressourcen $ress_operator, \tacitus89\rsp\operators\unternehmen $unternehmen_operator, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->container = $container;
		$this->ress_operator = $ress_operator;
		$this->unternehmen_operator = $unternehmen_operator;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->ext_path = 'ext/tacitus89/rsp/';
	}

	/**
	* Display
	*
	* @return null
	* @access public
	*/
	public function display()
	{
		$this->display_user_ress();
		$this->display_user_unternehmen($this->user->data['user_id']);

		// Send all data to the template file
		return $this->helper->render('rsp_uebersicht.html', $this->user->lang('RSP'));
    }

	/**
	* Display
	*
	* @return null
	* @access public
	*/
	public function display_unternehmen($unternehmen = '')
	{
		$this->display_user_ress();

		//have a name of unternehmen?
		if($unternehmen != '')
		{
			//show all infos for display
			$this->display_unternehmen_by_name($unternehmen);

			// Set output vars for display in the template
			$this->template->assign_vars(array(
				'S_UNTERNEHMEN'			=> false,
				'S_EIGENES_UNTERNEHMEN'	=> true,
				'S_BETRIEB'				=> true,
			));
		}
		else
		{
			$this->display_user_unternehmen($this->user->data['user_id']);

			// Set output vars for display in the template
			$this->template->assign_vars(array(
				'S_UNTERNEHMEN'			=> true,
				'S_EIGENES_UNTERNEHMEN'	=> false,
				'S_BETRIEB'				=> false,
			));
		}

		// Send all data to the template file
		return $this->helper->render('rsp_unternehmen.html', $this->user->lang('RSP'));
    }

	/**
	* Display all user unternehmen
	*
	* @return null
	* @access public
	*/
	public function display_user_unternehmen($user_id)
	{
		// Grab all the unternehmen
		$all_unternehmen = $this->unternehmen_operator->get_all_user_unternehmen($user_id);

		// Process each unternehmen entity for display
		foreach ($all_unternehmen as $unternehmen)
		{
			// Grab all the unternehmen_betriebe
			$unternehmen_betriebe = $this->unternehmen_operator->get_all_betriebe_of_unternehmen($unternehmen->get_id());

			// Set output block vars for display in the template
			$this->template->assign_block_vars('unternehmen_block', array(
				'ID'			=> $unternehmen->get_id(),
				'NAME'			=> $unternehmen->get_name(),
				'URL'			=> $this->helper->route('tacitus89_rsp_main_controller_unternehmen', array('unternehmen' => $unternehmen->get_name())),
			));

			// Process each unternehmen_betriebe entity for display
			foreach ($unternehmen_betriebe as $betrieb)
			{
				// Set output block vars for display in the template
				$this->template->assign_block_vars('unternehmen_block.betriebe', array(
					'NAME'			=> $betrieb->get_betrieb()->get_gebaude()->get_name(),
					'STUFE'			=> $betrieb->get_betrieb()->get_stufe(),
					'PROVINZ_NAME'	=> $betrieb->get_provinz()->get_name(),
				));
			}

		}

		//display navlinks of unternehmen
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'		=> $this->helper->route('tacitus89_rsp_main_controller_unternehmen'),
			'FORUM_NAME'		=> $this->user->lang('UNTERNEHMEN'),
		));
    }

	/**
	* Display one unternehmen by name
	*
	* @return null
	* @access public
	*/
	public function display_unternehmen_by_name($unternehmen)
	{
		// Grab the unternehmen
		$unternehmen = $this->container->get('tacitus89.rsp.entity.unternehmen')->load_by_name($unternehmen);

		// Grab all the unternehmen_betriebe
		$unternehmen_betriebe = $this->unternehmen_operator->get_all_betriebe_of_unternehmen($unternehmen->get_id());

		// Process each unternehmen_betriebe entity for display
		foreach ($unternehmen_betriebe as $betrieb)
		{
			// Set output block vars for display in the template
			$this->template->assign_block_vars('betrieb_block', array(
				'ID'			=> $betrieb->get_id(),
				'NAME'			=> $betrieb->get_betrieb()->get_gebaude()->get_name(),
				'STUFE'			=> $betrieb->get_betrieb()->get_stufe(),
				'PROVINZ_NAME'	=> $betrieb->get_provinz()->get_name(),
			));

			//Get all necessary ress of this betrieb
			$all_betrieb_ress = $this->ress_operator->get_ress_for_betrieb($betrieb->get_betrieb()->get_gebaude()->get_id());

			// Process each betrieb_ress entity for display
			foreach ($all_betrieb_ress as $ress)
			{
				// Set output block vars for display in the template
				$this->template->assign_block_vars('betrieb_block.ress', array(
					'NAME'			=> $ress->get_ressource()->get_name(),
					'MENGE'			=> $ress->get_menge(),
				));
			}


		}

		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'S_UNTERNEHMEN'			=> false,
			'S_EIGENES_UNTERNEHMEN'	=> true,
			'S_BETRIEB'				=> true,
		));

		//display navlinks of unternehmen
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'		=> $this->helper->route('tacitus89_rsp_main_controller_unternehmen'),
			'FORUM_NAME'		=> $this->user->lang('UNTERNEHMEN'),
		));

		//display navlinks for unternehmen
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'		=> $this->helper->route('tacitus89_rsp_main_controller_unternehmen', array('unternehmen' => $unternehmen->get_name())),
			'FORUM_NAME'		=> $unternehmen->get_name(),
		));
    }

    /**
	* Display user ress
	*
	* @return null
	* @access public
	*/
	public function display_user_ress()
	{
		// Grab all the ress
		$all_user_ress = $this->ress_operator->get_all_user_ress($this->user->data['user_id']);

		// Process each ress entity for display
		foreach ($all_user_ress as $user_ress)
		{

			// Set output block vars for display in the template
			$this->template->assign_block_vars('ress_'. $user_ress->get_ress()->get_bereich_id() .'_block', array(
				'NAME'			=> $user_ress->get_ress()->get_name(),
				'MENGE'			=> $user_ress->get_menge(),
				'IMAGE'			=> append_sid($this->root_path.$this->ext_path.$user_ress->get_ress()->get_url()),
			));
		}

		//display navlinks of wisim
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'		=> $this->helper->route('tacitus89_rsp_main_controller'),
			'FORUM_NAME'		=> $this->user->lang('WISIM'),
		));
    }
}
