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

class Ressourcen
{
	//allgemine Ressourcen des Users
	private $ress = array();
	
	private $ressMenge = array();
	private $ressName = array();
	private $ressUrl = array();
    private $bereich = array();


	
	public function __construct()
	{
		$this->BereicheAuslesen();
		$this->ressourcenNachBereich();
	}
	
	private function BereicheAuslesen()
	{
		global $db;
		
		$sql = 'SELECT id, name
			FROM ' . RSP_RESSOURCEN_BEREICH_TABLE;
		$result = $db->sql_query($sql);
		
		while($row = $db->sql_fetchrow($result))
		{
			$this->bereich[$row['id']] = $row['name'];
		}
	}
	
	private function ressourcenNachBereich()
	{
		global $db, $user;

		$sql = 'SELECT a.id, a.name, a.bereich_id, a.url, b.menge
			FROM ' . RSP_RESSOURCEN_TABLE . ' a
			LEFT JOIN ' . RSP_USER_RESS_TABLE . " b ON b.ress_id = a.id
			WHERE b.user_id = " . $user->data['user_id'];
		$result = $db->sql_query($sql);
		
		while($row = $db->sql_fetchrow($result))
		{
			$this->ressMenge[$row['bereich_id']][$row['id']] = $row['menge'];
			$this->ressName[$row['bereich_id']][$row['id']] = $row['name'];
			$this->ressUrl[$row['bereich_id']][$row['id']] = $row['url'];
			$this->ress[$row['name']] = $row['menge'];
		}
		
		$db->sql_freeresult($result);
	}
	
	public function ressNachName($name)
	{
		return $this->ress[$name];
	}
	
	public function ressourcenAusgabe()
	{
		global $template;
		global $phpbb_root_path;
		
		//Durchläuft alle Bereiche
		foreach($this->ressMenge AS $bereich_id => $bereiche)
		{
			//Durchläuft alle Ress in dem Bereich]
			foreach($bereiche AS $ress_id => $value)
			{
				$template->assign_block_vars('ress_'. $bereich_id .'_block', array(
					'RESS_NAME'		=> $this->ressName[$bereich_id][$ress_id],
					'RESS_URL'		=> $phpbb_root_path.$this->ressUrl[$bereich_id][$ress_id],
					'BEREICH_NAME'	=> $this->bereich[$ress_id],
					'MENGE'			=> $value,
                    'MAX_MENGE'     => ($ress_id == 1)? MAX_CREDITS : MAX_RESS,
				));
			}
		}
	}

    public static function idToImage($id)
    {
        global $db;

        $sql = 'SELECT url
			FROM ' . RSP_RESSOURCEN_TABLE . "
			WHERE id = $id";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);

        return $row['url'];
    }
	
	public static function ressListe()
	{
		global $db, $template;
		
		$sql = 'SELECT a.name AS BereichName, b.bereich_id, b.id, b.name
			FROM ' . RSP_RESSOURCEN_BEREICH_TABLE . ' a
			LEFT JOIN ' . RSP_RESSOURCEN_TABLE . " b ON b.bereich_id = a.id";
		$result = $db->sql_query($sql);
		
		$bereich = 0;
		while($row = $db->sql_fetchrow($result))
		{
			if($bereich != $row['bereich_id'])
			{
				$template->assign_block_vars('ress_block', array(
					'ID'		=> -1-$bereich,
					'NAME'		=> $row['BereichName'],
				));
				
				$bereich = $row['bereich_id'];
			}
			
			$template->assign_block_vars('ress_block', array(
				'ID'		=> $row['id'],
				'NAME'		=> '&nbsp;&nbsp;-' . $row['name'],
			));
		}
		
		$db->sql_freeresult($result);
	}
	
	public static function idToName($id)
	{
		global $db;
		
		$sql = 'SELECT name
			FROM ' . RSP_RESSOURCEN_TABLE . "
			WHERE id = $id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		return $row['name'];
	}
}

?>