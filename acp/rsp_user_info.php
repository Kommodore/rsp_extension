<?php
/**
*
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\acp;
	
class rsp_user_info
{
	public function module()
	{
		return array(
			'filename'	=> '\tacitus89\rsp\acp\rsp_user_module',
			'title'		=> 'ACP_RSP',
			'modes'		=> array(
				'user'			=> array('title' => 'RSP_USER', 'auth' => 'ext_tacitus89/rsp && acl_a_board',	'cat' =>  array('ACP_RSP')),
				'rank'			=> array('title' => 'RSP_USER_RANK', 'auth' => 'ext_tacitus89/rsp && acl_a_board', 'cat' => array('ACP_RSP')),
				'wirtschaft'	=> array('title' => 'RSP_WIRTSCHAFT', 'auth' => 'ext_tacitus89/rsp && acl_a_board', 'cat' =>  array('ACP_RSP')),
			),
		);
	}
}