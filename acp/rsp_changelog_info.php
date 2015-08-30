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
	
class rsp_changelog_info
{
	public function module()
	{
		return array(
			'filename'	=> '\tacitus89\rsp\acp\rsp_changelog_module',
			'title'		=> 'ACP_RSP',
			'modes'		=> array(
				'add'		=> array('title' => 'RSP_CHANGELOG_ADD', 'auth' => 'ext_tacitus89/rsp && acl_a_board',	'cat' =>  array('ACP_RSP')),
				'manage'	=> array('title' => 'RSP_CHANGELOG_MANAGE', 'auth' => 'ext_tacitus89/rsp && acl_a_board',	'cat' =>  array('ACP_RSP')),
			),
		);
	}
}