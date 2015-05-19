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

class Handel
{
	private $empfaenger_id;
	private $text;
	private $ress;
	private $menge;
	
	public function __construct($empfaenger,$text,$ress,$menge)
	{
		global $phpbb_root_path, $phpEx, $user;
		
		$this->empfaenger_id = $this->nameToID($empfaenger);
		$this->text = $text;
		$this->ress = $ress;
		$this->menge = $menge;

		if($this->empfaenger_id == 0)
		{
			//Meldung
			$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel');
			meta_refresh(5, $meta_url);
			$message = 'Fehler beim Senden! <span style="font-weight:bold">'. $empfaenger .'</span> ist unbekannt!';
			$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel') . '">', '</a>');
			trigger_error($message);
		}
	}
	
	private function nameToID($empfaenger)
	{
		global $phpbb_root_path, $phpEx, $user,$db;
		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
		
		$user_id = array();
		$user_id[] = $empfaenger;
		// User ID's to add...
		$user_id_ary = array();

		// Reveal the correct user_ids
		if (sizeof($user_id))
		{
			$user_id_ary = array();
			user_get_id_name($user_id_ary, $user_id, array(USER_NORMAL, USER_FOUNDER, USER_INACTIVE));

			// If there are users not existing, we will at least print a notice...
			if (!sizeof($user_id_ary))
			{
				//Meldung
				$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel');
				meta_refresh(5, $meta_url);
				$message = 'Den Empfänger <span style="font-weight:bold">'. $empfaenger .'</span> gibt es leider nicht!';
				$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel') . '">', '</a>');
				trigger_error($message);
				return 0;
			}
			else 
			{
				$gesuchte_id = $user_id_ary[0];
				
				$sql = 'SELECT user_rsp
					FROM ' . USERS_TABLE . '
					WHERE user_id = '. $gesuchte_id;
				$result = $db->sql_query($sql);
				$rsp_user = (int) $db->sql_fetchfield('user_rsp');
				$db->sql_freeresult($result);
				
				if($rsp_user)
				{
					return $gesuchte_id;
				}
				else 
				{
					//Meldung
					$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel');
					meta_refresh(5, $meta_url);
					$message = 'Den Empfänger <span style="font-weight:bold">'. $empfaenger .'</span> ist leider kein RSP-Spieler!';
					$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel') . '">', '</a>');
					trigger_error($message);
					return 0;
				}
			}
			
			return 0;
		}
	}
	
	public function senderGenugRess()
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
	
		$sql = 'SELECT menge
			FROM ' . RSP_USER_RESS_TABLE . '
			WHERE user_id = '. $user->data['user_id'] .'
				and ress_id = '. $this->ress .'
				and menge >= '. $this->menge;
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
	
	public function handelAbschliessen()
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		$sql = 'INSERT INTO ' . RSP_HANDEL_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'sender_id'	=> (int) $user->data['user_id'],
			'empfaenger_id'	=> (int) $this->empfaenger_id,
			'zweck_text' => (string) htmlspecialchars_decode($this->text),
			'ressource_art' => (int) $this->ress,
			'menge' => (int) $this->menge,
			'time' => (int) time(),
			'status' => (int) 0, //0 = unterwegs, 1 = angekommen
		));
		$db->sql_query($sql);
        $last_id = $db->sql_nextid();

        Log::add_log(RSP_LOG_HANDEL, 0, $last_id, 0);
		
		//Einem selbst
		$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . '
			SET menge = menge-'. $this->menge .'
			WHERE user_id = '. $user->data['user_id'] .' and
				ress_id = '. $this->ress .'';
		$db->sql_query($sql);
		
		add_log('rsp',  $this->empfaenger_id, 'LOG_RSP_HANDEL', $this->menge, Ressourcen::idToName($this->ress), $this->text);
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel');
		meta_refresh(5, $meta_url);
		$message = 'Dein Handel mit ist erfolgreich ausgeführt worden.<br />Du hast <span style="font-weight:bold">'. $this->menge .'</span> an <span style="font-weight:bold">'. Ressourcen::idToName($this->ress) .'</span> verschickt.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=handel') . '">', '</a>');
		trigger_error($message);
	}
	
	public static function bildeHandelliste()
	{
		global $db, $user, $template;
		
		$start				= request_var('start', 0);
		
		//Zeiteinteilung
		//$zeit = getdate();
		//$tag = mktime(0,0,0,$zeit['mon'],$zeit['mday'],$zeit['year']);

		$sql = 'SELECT a.sender_id, a.empfaenger_id, a.zweck_text, a.time, a.menge, a.status, b.username AS Sendername, b.user_colour AS SenderColor, c.username AS Empfaengername, c.user_colour AS EmpfaengerColor, d.name
			FROM ' . RSP_HANDEL_LOG_TABLE . ' a
			LEFT JOIN ' . USERS_TABLE . ' b ON b.user_id = a.sender_id
			LEFT JOIN ' . USERS_TABLE . ' c ON c.user_id = a.empfaenger_id
			LEFT JOIN ' . RSP_RESSOURCEN_TABLE . ' d ON d.id = a.ressource_art
			WHERE (a.sender_id = ' . $user->data['user_id'] .'
				OR a.empfaenger_id = ' . $user->data['user_id'] .' )
			ORDER BY a.time DESC';
		$result = $db->sql_query_limit($sql, 30, $start);
		
		while($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('handel_block', array(
				'SENDER'		=> get_username_string('full', $row['sender_id'], $row['Sendername'], $row['SenderColor']),
				'EMPFAENGER'	=> get_username_string('full', $row['empfaenger_id'], $row['Empfaengername'], $row['EmpfaengerColor']),
				'ZWECK'			=> $row['zweck_text'],
				'RESS'			=> $row['name'],
				'MENGE'			=> $row['menge'],
				'ZEIT'			=> $user->format_date($row['time']),
				'STATUS'		=> ($row['status'] == 0)? 'Unterwegs' : 'Beendet',
			));
		}
		
		$db->sql_freeresult($result);
		
		Handel::handelPage($start);
	}

	public static function handelPage($start)
	{
		global $db, $user, $template;
		global $phpbb_root_path, $phpEx;
		
		$sql = 'SELECT COUNT(id) as handel
			FROM ' . RSP_HANDEL_LOG_TABLE . '
			WHERE (sender_id = ' . $user->data['user_id'] .'
				OR empfaenger_id = ' . $user->data['user_id'] .' )';
		$result = $db->sql_query($sql);
		$total_handel = (int) $db->sql_fetchfield('handel');
		$db->sql_freeresult($result);
		
		$pagination = generate_pagination(append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=handel"), $total_handel, 30, $start);
		
		$template->assign_vars(array(
			'PAGINATION' 			=> $pagination,
			'PAGE_NUMBER'			=> on_page($total_handel, 30, $start),
			'TOTAL_HANDEL'			=> $user->lang('VIEW_HANDEL', $total_handel),
		));
	} 
}

?>