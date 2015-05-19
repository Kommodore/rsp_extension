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
* @package module_install
*/
class acp_rsp_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_rsp',
			'title'		=> 'ACP_RSP',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'overview'			=> array('title' => 'ACP_RSP_OVERVIEW',		'auth' => 'acl_a_rsp_overview',	'cat' => array('SZ_RSP')),
				'rank'				=> array('title' => 'ACP_RSP_USER_RANK',		'auth' => 'acl_a_rsp_overview',	'cat' => array('SZ_RSP')),
				'wirtschaft'		=> array('title' => 'ACP_RSP_WIRTSCHAFT',		'auth' => 'acl_a_rsp_overview',	'cat' => array('SZ_RSP')),
			),
		);
	}
}


?>