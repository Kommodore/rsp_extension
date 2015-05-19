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


class BenutzerUnternehmen
{	
	public $besitzer_id;
	public $unternehmen = array();
	
	//BenutzerUnternehmen
	// ID = userId
	public function __construct($id)
	{
		$this->besitzer_id = $id;
		$this->info();
	}
	
	//Alle wichtigen Infos zum BenutzerUnternehmen
	private function info()
	{
		global $db;
	
		$sql = 'SELECT id
			FROM ' . RSP_UNTERNEHMEN_TABLE . '
			WHERE user_id = ' . $this->besitzer_id . '
			ORDER BY id ASC';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$this->unternehmen[$row['id']] = new Unternehmen($row['id']);
		}
		$db->sql_freeresult($result);
	}
	
	public function unternehmenErstellen($name,$gueterbereich)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		
		//Kosten abheben
		$sql = 'SELECT kosten_unternehmen, name
			FROM ' . RSP_GUETERBEREICH_TABLE .'
			WHERE id = '. $gueterbereich;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . "
			SET menge = menge-". $row['kosten_unternehmen'] ."
			WHERE user_id = " . $user->data['user_id'] .'
				and ress_id = 1';
		$db->sql_query($sql);
		
		
		$sql = 'INSERT INTO ' . RSP_UNTERNEHMEN_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'user_id'	=> (int) $user->data['user_id'],
			'name'	=> (string) htmlspecialchars_decode($name),
			'gueterbereich' => (int) $gueterbereich)
		);
		$db->sql_query($sql);
		
				$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_rsp_anzahl_unternehmen = user_rsp_anzahl_unternehmen+1
			WHERE user_id = " . $user->data['user_id'];
		$db->sql_query($sql);
		
				
		add_log('rsp', 0, 'LOG_RSP_NEUES_UNTERNEHMEN', $name);
		
		//
		// Logo braucht die ID des neuen Unternehmens
		$nextID = $db->sql_nextid();
		$logo = BenutzerUnternehmen::logo_process_unternehmen($nextID);
		if($logo != FALSE)
		{
			$sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
				SET logo_url = '$logo'
				WHERE id = " . $nextID;
			$db->sql_query($sql);
		}
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen');
		meta_refresh(5, $meta_url);
		$message = 'Du hast erfolgreich dein Unternehmen <span style="font-weight:bold">'. $name .'</span> im Bereich <span style="font-weight:bold">'. $row['name'] .'</span> erstellen.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen') . '">', '</a>');
		trigger_error($message);
	}
	
	//Erstellt den "unternehmen_block"
	public function listeUnternehmen()
	{
		global $template, $user;
		global $phpbb_root_path, $phpEx;
		
		foreach($this->unternehmen as $value)
		{
			$loeschbar = false;
			//Ausgeben der Betriebe
			if ($value->anzahl_betriebe != 0)
			{
				$betrieb = $value->einfacheListeBetriebe();
			}
			else
			{ 
				$betrieb = '<dd>Hat kein Betrieb</dd>';
				//Wenn das Unternehmen keine Betriebe mehr hat - kann gel�scht werden
				$loeschbar = true;
			}
			
			$template->assign_block_vars('unternehmen_block', array(
				'UNTERNEHMEN_NAME'			=> $value->name,
				'UNTERNEHMEN_LOGO'		=> ($value->logo != '') ? ($phpbb_root_path . "download/file.$phpEx?logo=" .$value->logo) : false,
				'UNTERNEHMEN_URL'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;i=". $value->unternehmen_id),
				'UNTERNEHMEN_URL_AENDERN'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;action=edit&amp;i=". $value->unternehmen_id),
				'BETRIEBLISTE'			=> $betrieb,
			));
		}
	}
	
	public static function hatUserGenugGeld($credits,$gueterbereich)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		$sql = 'SELECT kosten_unternehmen, name
			FROM ' . RSP_GUETERBEREICH_TABLE .'
			WHERE id = '. $gueterbereich;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		if($credits >= $row['kosten_unternehmen'])
		{
			$db->sql_freeresult($result);
			return true;
		}
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen');
		meta_refresh(5, $meta_url);
		$message = 'Du hast zuwenig Credits um ein Unternehmen im Bereich <span style="font-weight:bold">'. $row['name'] .'</span> zu erstellen.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen') . '">', '</a>');
		trigger_error($message);
		return false;
	}
	
	public static function logo_process_unternehmen($nextID)
	{
		global $config, $phpbb_root_path, $phpEx, $user;

		$upload = (file_exists($phpbb_root_path . $config['avatar_path']) && phpbb_is_writable($phpbb_root_path . $config['avatar_path']) && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;
	
		if (sizeof($error) && $can_upload == FALSE)
		{
			return false;
		}
	
		// Init upload class
		include_once($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
		$upload = new fileupload('AVATAR_', array('jpg', 'jpeg', 'gif', 'png'), $config['avatar_filesize'], $config['avatar_min_width'], $config['avatar_min_height'], $config['avatar_max_width'], $config['avatar_max_height'], (isset($config['mime_triggers']) ? explode('|', $config['mime_triggers']) : false));
	
		if (!empty($_FILES['logoupload']['name']))
		{
			$file = $upload->form_upload('logoupload');
			
			$prefix = $config['avatar_salt'] . '_';
			$file->clean_filename('avatar', $prefix, $nextID);
			$destination = 'images/rsp_logo';

			// Adjust destination path (no trailing slash)
			if (substr($destination, -1, 1) == '/' || substr($destination, -1, 1) == '\\')
			{
				$destination = substr($destination, 0, -1);
			}
		
			$destination = str_replace(array('../', '..\\', './', '.\\'), '', $destination);
			if ($destination && ($destination[0] == '/' || $destination[0] == "\\"))
			{
				$destination = '';
			}
		
			// Move file and overwrite any existing image
			$file->move_file($destination, true);
		}
		else {
			return false;
		}
				
		if (sizeof($file->error))
		{
			$file->remove();
			//Meldung
			$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen');
			meta_refresh(5, $meta_url);
			$message = implode('<br />', $file->error);
			$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen') . '">', '</a>');
			trigger_error($message);
		}
			
		
		return ($nextID . '_' . time() . '.' . $file->get('extension'));
	}
}

?>