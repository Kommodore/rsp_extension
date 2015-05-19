<?php
/** 
*
* @package rsp
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


class Rekrutierung
{	
	public $user_id;
	public $einheiten_art = array();

	// ID = userId
	public function __construct($id)
	{
		$this->user_id = $id;
		$this->info();
	}
	
	//Alle wichtigen Infos zum BenutzerUnternehmen
	private function info()
	{
		global $db;
	
		$sql = 'SELECT id, name
			FROM ' . RSP_EINHEITEN_ART_TABLE . '
			ORDER BY id ASC';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$this->einheiten_art[$row['id']] = $row['name'];
		}
		$db->sql_freeresult($result);
	}

    public function show()
    {
        global $template;

        foreach($this->einheiten_art as $key => $value)
        {
            $template->assign_block_vars('einheiten_art', array(
                'ID'		=> $key,
                'NAME'		=> $value,
            ));
        }
    }
}

?>