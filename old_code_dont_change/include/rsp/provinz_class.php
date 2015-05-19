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


class Provinz
{
    private $info = array(
        'id'        => 0,
        'name'      => '',
        'hstadt'    => '',
        'land_id'   => 0,
        'land_name' => '',
    );
    private $rohstoffe = array();
    private $betriebe = array();

    public function __construct($id)
    {
        global $db;

        $sql = 'SELECT p.id, p.name, p.hstadt, l.id AS land_id, l.name AS land_name
			FROM ' . RSP_PROVINZEN_TABLE . ' p
			LEFT JOIN '. RSP_LAND_TABLE .' l ON l.id = p.land
			WHERE p.id = ' . $id;
        $result = $db->sql_query($sql);

        if($row = $db->sql_fetchrow($result))
        {
            $this->info['id'] = $row['id'];
            $this->info['name'] = $row['name'];
            $this->info['hstadt'] = $row['hstadt'];
            $this->info['land_id'] = $row['land_id'];
            $this->info['land_name'] = $row['land_name'];
        }
        $db->sql_freeresult($result);

        $this->infoRohstoffe();
    }

    private function infoRohstoffe()
    {
        global $db;

        $sql = 'SELECT p.betrieb_id, p.max_menge, p.aktuelle_menge, g.name
			FROM ' . RSP_PROVINZ_ROHSTOFF_TABLE . ' p
			LEFT JOIN '. RSP_BETRIEBE_TABLE .' b ON b.id = p.betrieb_id
			LEFT JOIN '. RSP_GEBAUDE_INFO_TABLE .' g ON g.id = b.gebaude_id
			WHERE p.provinz_id = ' . $this->info['id'];
        $result = $db->sql_query($sql);

        while($row = $db->sql_fetchrow($result))
        {
            $this->rohstoffe[$row['betrieb_id']] = array(
                'name'              => $row['name'],
                'max_menge'         => $row['max_menge'],
                'aktuelle_menge'    => $row['aktuelle_menge'],
            );
        }
        $db->sql_freeresult($result);

        $this->infoRohstoffBetriebe();
    }

    private function infoRohstoffBetriebe()
    {
        global $db;

        $sql = 'SELECT ub.id, ub.betrieb_id, g.name, u.name AS unternehmen_name, u.id AS unternehmen_id
			FROM ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . ' ub
			LEFT JOIN '. RSP_BETRIEBE_TABLE .' b ON b.id = ub.betrieb_id
			LEFT JOIN '. RSP_GEBAUDE_INFO_TABLE .' g ON g.id = b.gebaude_id
			LEFT JOIN '. RSP_UNTERNEHMEN_TABLE .' u ON u.id = ub.unternehmen_id
			WHERE ub.provinz_id = ' . $this->info['id'] .'
			    AND ub.betrieb_id <= 10' ; //TODO: Was besseres einfallen lassen
        $result = $db->sql_query($sql);

        while($row = $db->sql_fetchrow($result))
        {
            $this->rohstoffe[$row['betrieb_id']]['betriebe'][$row['id']] = array(
                'name'              => $row['name'],
                'unternehmen_name'  => $row['unternehmen_name'],
                'unternehmen_id'    => $row['unternehmen_id'],
            );
        }
        $db->sql_freeresult($result);
    }

    public function toTemplate()
    {
        global $template, $phpbb_root_path, $phpEx;

        $template->assign_vars(array(
            'S_ALLE_PROVINZEN '	=> FALSE,
            'S_PROVINZ'		=> TRUE,
            'U_PROVINZ_ID'	=> $this->info['id'],
        ));

        foreach ($this->rohstoffe AS $key => $roh)
        {
            $template->assign_block_vars('rohstoff_block', array(
                'NAME'				=> $roh['name'],
                'MAX_MENGE'			=> $roh['max_menge'],
                'AKT_MENGE'         => $roh['aktuelle_menge'],
                'EXISTS_BETRIEBE'   => (is_array($roh['betriebe']))? true:false,
                'FARBE'             => ($roh['aktuelle_menge'] > 0)? 'green': 'red',
            ));
            if(is_array($roh['betriebe']))
            {
                foreach ($roh['betriebe'] AS $betriebe)
                {
                    $template->assign_block_vars('rohstoff_block.betriebe', array(
                        'NAME'              => $betriebe['name'],
                        'UNTERNEHMEN_NAME'  => $betriebe['unternehmen_name'],
                        'UNTERNEHMEN_URL'    => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;i=". $betriebe['unternehmen_id']),
                    ));
                }
            }
        }
    }

	public static function idToName($id)
	{
		global $db;
		
		$sql = 'SELECT name
			FROM ' . RSP_PROVINZEN_TABLE . "
			WHERE id = $id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		return $row['name'];
	}
}

?>