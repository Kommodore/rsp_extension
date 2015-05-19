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

class Betrieb
{	
	public $gebaude_id;
	public $name;
	public $art;
	public $ort;
    public $bild_url;
	public $produktion_id;
    public $aktuelle_produktion;
	public $max_produktion;
    public $anzahl_produktion;
	public $rohstoff = array();
	
	//Betrieb mit bekannter ID
	public function __construct($id, $rohstoffe = false)
	{
		$this->gebaude_id = $id;
		$this->info();
		//Den Betriebe werden die Rohstoffe mit einbezogen
		if($rohstoffe == true)
		{
			$this->betrieb_rohstoffe();
		}
	}
	
	//Alle wichtigen Infos zum Betrieb
	private function info()
	{
		global $db;
	
		$sql = 'SELECT a.gebaude_id, a.provinz_id, a.aktuelle_produktion, a.anzahl_produktion, b.name, b.bild_url, b.produktion_id, b.max_produktion
			FROM ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . ' a
			LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = a.gebaude_id
			WHERE a.id = ' . $this->gebaude_id . '
			ORDER BY b.id ASC';
		$result = $db->sql_query($sql);
		$info = $db->sql_fetchrow($result);
		if(!$info)
		{
			$info = false;
		}
		else
		{
			$this->name = $info['name'];
			$this->art = $info['gebaude_id'];
			$this->ort = $info['provinz_id'];
            $this->bild_url = $info['bild_url'];
			$this->produktion_id = $info['produktion_id'];
			$this->max_produktion = $info['max_produktion'];
            $this->aktuelle_produktion = $info['aktuelle_produktion'];
			$this->anzahl_produktion = $info['anzahl_produktion'];
			$db->sql_freeresult($result);
		}
	}
	
	public function betrieb_rohstoffe()
	{
		global $db;
		
		$sql = 'SELECT a.ressourcen_id, a.menge AS RessourcenMenge, b.name AS RessourcenName
			FROM ' . RSP_BETRIEBE_ROHSTOFFE_TABLE . ' a
			LEFT JOIN ' . RSP_RESSOURCEN_TABLE . ' b ON a.ressourcen_id = b.id
			WHERE a.gebaude_id = ' . $this->art . '
			ORDER BY a.menge DESC, a.id ASC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			
			if($row['RessourcenName'] != NULL & $row['RessourcenMenge'] != 0)
			{
				$this->rohstoff[$row['ressourcen_id']] = array( 'menge' => $row['RessourcenMenge'], 'name' => $row['RessourcenName']);
			}
			else
			{
				$this->rohstoff['0'] = array( 'menge' => 0, 'name' => "Keine");
			}
		}
	}
	
	public function liste_betrieb_rohstoffe()
	{
		$ress = "";
		$zahl = 0;
		if(count($this->rohstoff) != 0)
		{
			foreach($this->rohstoff as $value)
			{
				$ress .= '<dd>'. $value['name'] .' (<span id="rohstoff-'. $this->gebaude_id .'-'.++$zahl.'">'. $value['menge'] .'<span>): <span id="rohstoff-'. $this->gebaude_id .'-'.++$zahl.'">0</span></dd>';
			}
		}
		else
		{
			$ress = '<dd>Keine</dd>';
		}
		return $ress;
	}

    public function getAuftrag()
    {
        global $db, $template;

        $sql = 'SELECT a.id, a.menge, a.time
			FROM ' . RSP_PRODUKTIONS_LOG_TABLE . ' a
			WHERE a.betrieb_id = ' . $this->gebaude_id . '
			AND a.status = 0
			ORDER BY a.time DESC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            $template->assign_block_vars('betrieb_block.auftrag', array(
                'ID'                => $row['id'],
                'MENGE'				=> $row['menge'],
                'TIME'              => $row['time'],
                'DATE'              => date("j.n.Y H:i:s", $row['time']+(24*60*60))
            ));
        }

    }
	
	public static function idToName($id)
	{
		global $db;
		
		$sql = 'SELECT name
			FROM ' . RSP_BETRIEBE_TABLE . "
			WHERE id = $id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		return $row['name'];
	}
}

?>