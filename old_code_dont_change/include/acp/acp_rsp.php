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
* @package acp
*/
class acp_rsp
{
	var $u_action;
	
	function main($id, $mode)
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		
		$username	= utf8_normalize_nfc(request_var('username', '', true));
		$user_id	= request_var('u', 0);
		$this->tpl_name = 'acp_rsp';
		$user->add_lang('mods/rsp_acp');
		add_form_key('acp_rsp');
		
		// Wähle den Benutzer aus
		if (!$username && !$user_id)
		{
			$template->assign_vars(array(
				'U_ACTION'			=> $this->u_action,

				'S_RSP_SELECT_USER'		=> true,
				'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=select_user&amp;field=username&amp;select_single=true'),
			));

			return;
		}
		
		if (!$user_id)
		{
			$sql = 'SELECT user_id
				FROM ' . USERS_TABLE . "
				WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";
			$result = $db->sql_query($sql);
			$user_id = (int) $db->sql_fetchfield('user_id');
			$db->sql_freeresult($result);

			if (!$user_id)
			{
				trigger_error($user->lang['NO_USER'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
		}	
		
		switch ($mode)
		{
			case 'rank':
				$title = 'ACP_RSP_USER_RANK';
				$this->user_rank($user_id,$username);
			break;
			
			case 'wirtschaft':
				$title = 'ACP_RSP_WIRTSCHAFT';
				$this->tpl_name = 'acp_rsp_wirtschaft';
				$this->rsp_wirtschaft($user_id,$username);
			break;

			default:
				$title = 'ACP_RSP_USER';
				$this->rsp_user($user_id,$username);
			break;
		}
		$this->page_title = $user->lang[$title];
	}
	
	//---------------------------------------------------------
	// Rang Funktion
	//---------------------------------------------------------
	function user_rank($user_id,$username)
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		
		$rsp_rang	= (int) request_var('rsp_rang', 0);
		$rsp_amt	= utf8_normalize_nfc(request_var('rsp_amt', '', true));
		$submit 	= (isset($_POST['submit'])) ? true : false;

		//Benutzer Aemter und Rangbild laden
		$sql = 'SELECT r.url, r2.beruf as amt, u.user_rsp_rang, u.user_rsp_amt
				FROM ' . USERS_TABLE . ' u
				LEFT JOIN '. RSP_RAENGE_TABLE .' r ON r.id = u.user_rsp_rang
				LEFT JOIN '. RSP_RAENGE_TABLE .' r2 ON r2.id = u.user_rsp_amt
				WHERE u.user_id = ' . $user_id . '';
		$result = $db->sql_query($sql);
		if ($row = $db->sql_fetchrow($result))
		{
			$template->assign_vars(array(
				'U_RSP_USER_AMT_NON'	=> ($row['amt'] == 0) ? true:false,
				'U_RSP_USER_AMT_GOV'	=> ($row['amt'] == 'GOV')? true:false,
				'U_RSP_USER_AMT_PRA'	=> ($row['amt'] == 'PRA')? true:false,
				'U_RSP_USER_AMT_SEK'	=> ($row['amt'] == 'SEK')? true:false,
				'U_RSP_USER_AMT_MIN'	=> ($row['amt'] == 'MIN')? true:false,
				'U_RSP_USER_RANK_BILD'	=> $phpbb_root_path.$row['url'],
			));
			
			//Gibt es Aenderungen beim Rang?
			if($submit && $rsp_rang != 0 && $rsp_rang != $row['user_rsp_rang'])
			{
				$db->sql_freeresult($result);	
				if($rsp_rang == -2)
				{
					//Infos speichern
					$sql = 'UPDATE ' . USERS_TABLE . "
							SET user_rsp_rang = 0
							WHERE user_id = $user_id";
					$db->sql_query($sql);
					
					add_log('rsp_rang',  $user_id, 'LOG_RSP_NO_RANG');
					$db->sql_freeresult($result);
				}
				else
				{				
					$sql = 'SELECT name
						FROM '. RSP_RAENGE_TABLE ."
						WHERE id = $rsp_rang";
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					
					//Infos speichern
					$sql = 'UPDATE ' . USERS_TABLE . '
							SET user_rsp_rang = '. $rsp_rang ."
							WHERE user_id = $user_id";
					$db->sql_query($sql);
					
					add_log('rsp_rang',  $user_id, 'LOG_RSP_RANG', $row['name']);
					$db->sql_freeresult($result);
				}
			}
		
			
			//Gibt es aenderungen beim AMT?
			if ($submit && $rsp_amt != '' && $rsp_amt != $row['amt'])
			{
				$db->sql_freeresult($result);
				if($rsp_amt != 'NON')
				{
					$sql = 'SELECT id, name
						FROM '. RSP_RAENGE_TABLE ."
						WHERE beruf = '" . $rsp_amt . "'
							AND stufe = 0
							AND land = 0";
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					
					//Infos speichern
					$sql = 'UPDATE ' . USERS_TABLE . '
							SET user_rsp_amt = '. $row['id'] ."
							WHERE user_id = $user_id";
					$db->sql_query($sql);
					
					add_log('rsp_rang',  $user_id, 'LOG_RSP_AMT', $row['name']);
					$db->sql_freeresult($result);
				}
				elseif($rsp_amt == 'NON' && $row['user_rsp_amt'] != 0)
				{
					//Infos speichern
					$sql = 'UPDATE ' . USERS_TABLE . "
							SET user_rsp_amt = 0
							WHERE user_id = $user_id";
					$db->sql_query($sql);
					
					add_log('rsp_rang',  $user_id, 'LOG_RSP_NO_AMT');
				}
			}
			if ($submit)
				trigger_error($user->lang['Updated'] . adm_back_link($this->u_action));
		}
		else $db->sql_freeresult($result);

		
		//ALLE Raenge laden
		$sql = 'SELECT r.id, r.name, r.beruf, r.stufe, r.url, l.id AS land_id, l.name AS landname, u.user_rsp_rang
				FROM '. RSP_LAND_TABLE .' l
				LEFT JOIN ' . RSP_RAENGE_TABLE . ' r ON l.id = r.land
				LEFT JOIN ' . USERS_TABLE . " u ON u.user_id = $user_id
				ORDER BY l.id, r.beruf, r.stufe";
		$result = $db->sql_query($sql);
		$land = -1;
		
		//Manuelles einfuegen
		//dient zum loeschen des Ranges
		$template->assign_block_vars('user_rang', array(
				'ID'		=> -2,
				'NAME'		=> '&nbsp;&nbsp;&nbsp;>>> Rang entfernen <<<',
		));
		
		while($row = $db->sql_fetchrow($result))
		{
			if($land != $row['land_id'])
			{
				$template->assign_block_vars('user_rang', array(
					'ID'		=> -1,
					'NAME'		=> $row['landname'],
				));
				
				$land = $row['land_id'];
			}
			
			$template->assign_block_vars('user_rang', array(
				'ID'		=> $row['id'],
				'NAME'		=> '&nbsp;&nbsp;&nbsp;&nbsp;[' . $row['beruf'] ." - ".  $row['stufe'] . "] " . $row['name'],
				'OPTION'	=> ($row['id'] == $row['user_rsp_rang'])? 'selected="selected"': "",
			));
		}
		$db->sql_freeresult($result);
		
		$template->assign_vars(array(
			'S_RSP_USER_RANK' 	=> true,
			'U_BACK'			=> $this->u_action,
			'U_MODE_SELECT'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=$id&amp;u=$user_id"),
			'U_ACTION'			=> $this->u_action . '&amp;u=' . $user_id,
			'S_FORM_OPTIONS'	=> $s_form_options,
			'MANAGED_USERNAME'	=> $username,
		));
	}
	
	//---------------------------------------------------------
	// Benutzer Funktion
	//---------------------------------------------------------
	function rsp_user($user_id,$username)
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		

		$create_rsp_user = request_var('create_rsp_user', '');
		$delete_rsp_user = request_var('delete_rsp_user', '');
		$rsp_username = utf8_normalize_nfc(request_var('rsp_username', '', true));
		$rsp_land = utf8_normalize_nfc(request_var('rsp_land', '', true));
		$submit 	= (isset($_POST['submit'])) ? true : false;
		
		// Ist der User schon ein RSP-Spieler???
		$sql = 'SELECT user_id, user_rsp
				FROM ' . USERS_TABLE . "
				WHERE user_id = '" . $user_id . "'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		if ($row['user_rsp'] == 1)
		{	// Ja ist er
			$user_rsp = true;
			
			$template->assign_vars(array(
				'S_USER_IN_RSP' => true,
			));
		}
		else
		{	// Nein
			$user_rsp = false;
			$template->assign_vars(array(
				'S_USER_NOT_IN_RSP' => true,
				'CREATE_RSP_USER'	=> 'TRUE',
			));
		}
		$db->sql_freeresult($result);

		// RSP-Name???
		$sql = 'SELECT user_rsp_name, user_rsp_land_id
				FROM ' . USERS_TABLE . "
				WHERE user_id = '" . $user_id . "'";
		$result = $db->sql_query($sql);
		if ($row = $db->sql_fetchrow($result))
		{	// Ja ist er
			$rsp_name = $row['user_rsp_name'];
			$rsp_user_land_id = $row['user_rsp_land_id'];
		}
		$db->sql_freeresult($result);

		//RSP-LAND?
		$sql = 'SELECT id
				FROM '. RSP_LAND_TABLE ."
				WHERE kurz_name = '" . $rsp_land . "'";
		$result = $db->sql_query($sql);
		if ($row = $db->sql_fetchrow($result))
		{	//Ja, Land gibt es
			$rsp_land_id = $row['id'];
		}
		$db->sql_freeresult($result);
		
		$template->assign_vars(array(
			'S_RSP_USER_OVERVIEW'	=> true,
			'U_RSP_USERNAME'	=> $rsp_name,
			'U_BACK'			=> $this->u_action,
			'U_MODE_SELECT'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=$id&amp;u=$user_id"),
			'U_ACTION'			=> $this->u_action . '&amp;u=' . $user_id,
			'S_FORM_OPTIONS'	=> $s_form_options,
			'MANAGED_USERNAME'	=> $username,
			'U_RSP_USER_LAND_FRT'	=> ($rsp_user_land_id == 2) ? true : false,
			'U_RSP_USER_LAND_USR'	=> ($rsp_user_land_id == 3) ? true : false,
			'U_RSP_USER_LAND_VRB'	=> ($rsp_user_land_id == 4) ? true : false,
		));
		
		if ($submit)
		{						
			if ($rsp_username != '' && $user_rsp == true)
			{
				$this->change_rsp_username($user_id, $rsp_username, $rsp_land_id);
			}
			if ($create_rsp_user == true && $user_rsp == false && $rsp_username != '')
			{
				$this->new_rsp_user($user_id, $rsp_username);
				$this->change_rsp_username($user_id, $rsp_username, $rsp_land_id);
			}
			if ($delete_rsp_user == true && $user_rsp == true)
			{
				$this->delete_rsp_user($user_id);
			}
			trigger_error($user->lang['Updated'] . adm_back_link($this->u_action));
		}
	}
	
	function new_rsp_user ($user_id)
	{
		global $db;
		
		// User ist nun RSP-Spieler
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_rsp = 1
			WHERE user_id = $user_id";
		$db->sql_query($sql);
		
		//RessourcenTabellen werden erstellt.
		$sql = 'SELECT id
			FROM ' . RSP_RESSOURCEN_TABLE .'
			ORDER BY id ASC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$sql = 'INSERT INTO ' . RSP_USER_RESS_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'user_id'			=> (int) $user_id,
			'ress_id'			=> (int) $row['id'],
			'menge'				=> (int) 0,
			));
			$db->sql_query($sql);
		}
		
		return;
	}
	
	function change_rsp_username($user_id, $rsp_username, $rsp_land_id)
	{
		global $db;
		
		$user_sql = array(
				'user_rsp_name'		=> $rsp_username,
				'user_rsp_land_id' 	=> $rsp_land_id,
			);
		//Infos speichern
		$sql = 'UPDATE ' . USERS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $user_sql) . "
				WHERE user_id = $user_id";
		$db->sql_query($sql);
		trigger_error($user->lang['Updated'] . adm_back_link($this->u_action));
	
		return;
	}
	
	function delete_rsp_user($user_id)
	{
		global $db;
		
		// User wird ausgeschlossen
		$user_sql = array(
				'user_rsp' => 0,
				'user_rsp_land_id' 	=> 0,
				'user_rsp_anzahl_unternehmen' => 0,
			);
		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $user_sql) . "
			WHERE user_id = $user_id";
		$db->sql_query($sql);
		
		
		// Alle Ressourcen verschwinden
		$sql = 'DELETE FROM ' . RSP_USER_RESS_TABLE . '
				WHERE user_id = ' . $user_id . '';
		$db->sql_query($sql);
		
		// Unternehmen werden neutral
		$sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
			SET user_id = 0
			WHERE user_id = $user_id";
		$db->sql_query($sql);
	
		return;
	}
	
	//---------------------------------------------------------
	// Wirschaft Funktion
	//---------------------------------------------------------
	function rsp_wirtschaft($user_id,$username)
	{
		global $db, $template, $user;
		global $phpbb_root_path, $phpEx;
		
		$rsp_ress_art = request_var('rsp_ress_art', 0);
		$rsp_ress_modus = utf8_normalize_nfc(request_var('rsp_ress_modus', '', true));
		$rsp_ress_menge = request_var('rsp_ress_menge', 0);
		$rsp_ress_text = utf8_normalize_nfc(request_var('rsp_ress_text', '', true));
		$submit = (isset($_POST['submit'])) ? true : false;
		
		// Ist der User ein RSP-Spieler???
		$sql = 'SELECT user_id, user_rsp
				FROM ' . USERS_TABLE . "
				WHERE user_id = '" . $user_id . "'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		if ($row['user_rsp'] == 0)
		{	// Nein
			$user_rsp = false;
			$template->assign_vars(array(
				'S_USER_IN_RSP' => false,
				'S_RSP_WIRTSCHAFT' => false,
				'S_RSP_SELECT_USER' => false,
			));
			
			$db->sql_freeresult($result);
		}
		else
		{	// Ja ist er
			$user_rsp = true;
			
			$template->assign_vars(array(
				'S_USER_IN_RSP' => true,
			));

			$db->sql_freeresult($result);
			
			//Die Ressourcenlisten
			$this->listeRessourcen();
			$this->listeUserRessourcen($user_id);
			
			$template->assign_vars(array(
				'S_RSP_WIRTSCHAFT'	=> true,
				'U_BACK'			=> $this->u_action,
				'U_MODE_SELECT'		=> append_sid("{$phpbb_admin_path}index.$phpEx", "i=$id&amp;u=$user_id"),
				'U_ACTION'			=> $this->u_action . '&amp;u=' . $user_id,
				'S_FORM_OPTIONS'	=> $s_form_options,
				'MANAGED_USERNAME'	=> $username,
			));
			

			
			if ($submit && $this->ressGibtES($rsp_ress_art))
			{
				switch($rsp_ress_modus)
				{
					case 'add':
						//Ress hinzufügen
						$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . "
							SET menge = menge+$rsp_ress_menge
							WHERE user_id = ". $user_id ." and
								ress_id = $rsp_ress_art";
						$db->sql_query($sql);
						
						//Handellog erzeugen
						$sql = 'INSERT INTO ' . RSP_HANDEL_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
							'sender_id'	=> (int) 59,
							'empfaenger_id'	=> (int) $user_id,
							'zweck_text' => (string) htmlspecialchars_decode($rsp_ress_text),
							'ressource_art' => (int) $rsp_ress_art,
							'menge' => (int) $rsp_ress_menge,
							'time' => (int) time(),
							'status' => 1,
						));
						$db->sql_query($sql);
						
						redirect($this->u_action . '&amp;u=' . $user_id);
					break;
					case 'sub':
						//Ress abziehen
						$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . "
							SET menge = menge-$rsp_ress_menge
							WHERE user_id = ". $user_id ." and
								ress_id = $rsp_ress_art";
						$db->sql_query($sql);
						
						//Handellog erzeugen
						$sql = 'INSERT INTO ' . RSP_HANDEL_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
							'sender_id'	=> (int) $user_id,
							'empfaenger_id'	=> (int) 59,
							'zweck_text' => (string) htmlspecialchars_decode($rsp_ress_text),
							'ressource_art' => (int) $rsp_ress_art,
							'menge' => (int) $rsp_ress_menge,
							'time' => (int) time(),
							'status' => 1,
						));
						$db->sql_query($sql);
						
						redirect($this->u_action . '&amp;u=' . $user_id);
					break;
				}
			}
			
		}
	}
	
	//Ressourcenliste erstellen
	function listeRessourcen()
	{
		global $db, $template;
		
		$sql = 'SELECT id, name
			FROM ' . RSP_RESSOURCEN_TABLE .'
			ORDER BY id ASC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('ress_block', array(
				'ID'		=> $row['id'],
				'NAME'		=> $row['name'],
			));
		}
		
		$db->sql_freeresult($result);
	}
	
	//Ressourcen des Users
	function listeUserRessourcen($user_id)
	{
		global $db, $template;
		
		$sql = 'SELECT a.name, b.menge
			FROM ' . RSP_RESSOURCEN_TABLE . ' a
			LEFT JOIN ' . RSP_USER_RESS_TABLE . " b ON b.ress_id = a.id
			WHERE b.user_id = " . $user_id;
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('user_ress_block', array(
				'NAME'		=> $row['name'],
				'MENGE'		=> $row['menge'],
			));
		}
		
		$db->sql_freeresult($result);
	}
	
	//Gibt es die Ressource???
	function ressGibtES($rsp_ress_art)
	{
		global $db;
		
		$sql = 'SELECT id
			FROM ' . RSP_RESSOURCEN_TABLE . ' a
			WHERE id = ' . $rsp_ress_art;
		$result = $db->sql_query($sql);
		
		if($db->sql_fetchrow($result))
		{
			$db->sql_freeresult($result);
			return true;
		}
		$db->sql_freeresult($result);
		return false;
	}
}