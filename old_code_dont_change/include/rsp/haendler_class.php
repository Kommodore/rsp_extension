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


class Haendler
{
	private $haendler_id = HAENDLER_ID;
	private $angebote = array();
	
	public function __construct()
	{
		$this->angeboteEinlesen();
	}
	
	private function angeboteEinlesen()
	{
		global $db;
		
		$sql = 'SELECT a.ressource_id, a.preis, b.name
			FROM ' . RSP_HAENDLER_TABLE . ' a
			LEFT JOIN '. RSP_RESSOURCEN_TABLE .' b ON b.id = a.ressource_id
			ORDER BY ressource_id ASC';
		$result = $db->sql_query($sql);
		
		while($row = $db->sql_fetchrow($result))
		{
			$this->angebote[$row['ressource_id']] = array('name' => $row['name'], 'preis' => $row['preis']);
		}
	}
	
	public function senderGenugRess($ress, $menge)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
	
		$sql = 'SELECT menge
			FROM ' . RSP_USER_RESS_TABLE . '
			WHERE user_id = '. $user->data['user_id'] .'
				and ress_id = '. $ress .'
				and menge >= '. $menge;
		$result = $db->sql_query($sql);
		
		if($db->sql_fetchrow($result))
		{
			$db->sql_freeresult($result);
			return true;
		}
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel');
		meta_refresh(5, $meta_url);
		$message = 'Du hast nicht genug <span style="font-weight:bold">'. Ressourcen::idToName($this->ress) .'</span> für diesen Handel.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel') . '">', '</a>');
		trigger_error($message);
		return false;
	}
	
	public function handelAbschliessen($ress, $menge)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		/*
		//Waren zum Händler
		$sql = 'INSERT INTO ' . RSP_HANDEL_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'sender_id'	=> (int) $user->data['user_id'],
			'empfaenger_id'	=> (int) $this->haendler_id,
			'zweck_text' => (string) htmlspecialchars_decode('Handel'),
			'ressource_art' => (int) $ress,
			'menge' => (int) $menge,
			'time' => (int) time(),
			'status' => (int) 0, //0 = unterwegs, 1 = angekommen
		));
		$db->sql_query($sql);
		*/
		
		//Zufallszahl generieren
		$menge_teil = $menge*HAENDLER_SPANNE/100;
		$zufall = ($this->randomFloat(($menge-$menge_teil), ($menge+$menge_teil)));
		
		//Credits zum Spieler
		$sql = 'INSERT INTO '. RSP_HANDEL_LOG_TABLE .' (sender_id, empfaenger_id, zweck_text, ressource_art, menge, sender_ress_art, sender_menge, time, status)
			SELECT  '. $user->data['user_id'] .', '. $this->haendler_id .', "'. htmlspecialchars_decode('Handel') .'",  '. $ress .', '. $menge .',
			1 , CONVERT (('. $zufall .'*b.preis), UNSIGNED),'. time() .', 0
			FROM '. RSP_HAENDLER_TABLE .' b
			WHERE b.ressource_id = '. $ress .'';
		$db->sql_query($sql);
        $last_id = $db->sql_nextid();

        Log::add_log(RSP_LOG_HAENDLER, 0, $last_id, 0);
		
		//Einem selbst
		$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . '
			SET menge = menge-'. $menge .'
			WHERE user_id = '. $user->data['user_id'] .' and
				ress_id = '. $ress .'';
		$db->sql_query($sql);
		
		add_log('rsp',  $this->haendler_id, 'LOG_RSP_HANDEL', $menge, Ressourcen::idToName($ress), 'Handel');
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel');
		meta_refresh(5, $meta_url);
		$message = 'Dein Handel mit dem Händler ist erfolgreich ausgeführt worden.<br />Du hast <span style="font-weight:bold">'. $menge .'</span> an <span style="font-weight:bold">'. Ressourcen::idToName($ress) .'</span> verschickt.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel') . '">', '</a>');
		trigger_error($message);
	}
	
	public function angeboteAusgeben()
	{
		global $template;
	
		//Durchläuft alle Anegobte
		foreach($this->angebote AS $key => $value)
		{
			$template->assign_block_vars('haendler_block', array(
				'NAME'		=> $value['name'],
				'ID'		=> $key,
				'PREIS'		=> $value['preis'],
			));
		}
		
		
	}
	
	private function randomFloat($min = 0, $max = 1) {
    	return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}
}

?>