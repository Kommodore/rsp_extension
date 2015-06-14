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

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/** @var string Path to ext dir */
	protected $dir;

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
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\pagination $pagination, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, ContainerInterface $container, \tacitus89\rsp\operators\ressourcen $ress_operator, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->pagination = $pagination;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->container = $container;
		$this->ress_operator = $ress_operator;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		if($this->config['enable_mod_rewrite'])
		{
			$this->dir = $this->root_path.'../ext/tacitus89/rsp/';
		}
		else {
			$this->dir = $this->root_path.'../../ext/tacitus89/rsp/';
		}
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

		// Send all data to the template file
		return $this->helper->render('rsp_uebersicht.html', $this->user->lang('RSP'));
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
				'IMAGE'			=> $this->dir.$user_ress->get_ress()->get_url(),
			));
		}
    }
}
