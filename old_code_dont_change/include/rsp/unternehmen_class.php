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

class Unternehmen
{	
	public $unternehmen_id;
	public $besitzer_id;
	public $name;
	public $logo;
	public $anzahl_betriebe;
	public $gueterbereich;
	public $betriebe = array();
	
	//Unternehmen mit bekannter ID
	public function __construct($id, $rohstoffe = false)
	{
		$this->unternehmen_id = $id;
		$this->info();
		$this->unternehmensBetriebe($rohstoffe);
	}
	
	//Alle wichtigen Infos zum Unternehmen
	private function info()
	{
		global $db;
	
		$sql = 'SELECT id, user_id, name, gueterbereich, anzahl_betriebe, logo_url
			FROM ' . RSP_UNTERNEHMEN_TABLE . '
			WHERE id = '. $this->unternehmen_id;
		$result = $db->sql_query($sql);
		$info = $db->sql_fetchrow($result);
		if(!$info)
		{
			$info = false;
		}
		else
		{	
			$this->besitzer_id = $info['user_id'];
			$this->name = $info['name'];
			$this->gueterbereich = $info['gueterbereich'];
			$this->anzahl_betriebe = $info['anzahl_betriebe'];
			$this->logo = $info['logo_url'];
			
			$db->sql_freeresult($result);
		}
	}
	
	//Erstellt die Betriebe fürs Unternehmen
	private function unternehmensBetriebe($rohstoffe)
	{
		global $db;
		
		$sql = 'SELECT id
			FROM ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . '
			WHERE unternehmen_id = '. $this->unternehmen_id;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			//Neues Objekt Betrieb
			$this->betriebe[$row['id']] = new Betrieb($row['id'], $rohstoffe);
		}
	}
	
	// Ist der Betrieb im Unternehmen?
	public function isBetriebInUnternehmen($betrieb_id)
	{
		foreach($this->betriebe as $value)
		{
			if($betrieb_id == $value->gebaude_id)
			{
				return true;
			}
		}
		return false;
	}
	
	public function produktion_erteilen()
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;


		 
		if($produktion_anzahl != 0 && $betrieb_id != 0 &&
         $this->isBetriebInUnternehmen($betrieb_id) && $this->genugRohstoffe($betrieb_id,$produktion_anzahl) &&
         $this->betriebe[$betrieb_id]->aktuelle_produktion < $this->betriebe[$betrieb_id]->max_produktion &&
        ($this->betriebe[$betrieb_id]->aktuelle_produktion + $produktion_anzahl) <= $this->betriebe[$betrieb_id]->max_produktion &&
        ($this->betriebe[$betrieb_id]->anzahl_produktion < 5))
		{
			 //User-Ress anpassen
			foreach($this->betriebe[$betrieb_id]->rohstoff as $k => $value)
			{
				$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . "
				SET menge = menge - ". ($value['menge']*$produktion_anzahl) ."
				WHERE user_id = ". $user->data['user_id'] ."
					and ress_id = $k";
				$db->sql_query($sql);
			}

            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . "
				SET aktuelle_produktion = ". ($this->betriebe[$betrieb_id]->aktuelle_produktion + $produktion_anzahl) .",
				anzahl_produktion = anzahl_produktion + 1
				WHERE id = $betrieb_id";
            $db->sql_query($sql);

			 // Auftrag erteilen
			 $sql = 'INSERT INTO ' . RSP_PRODUKTIONS_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'betrieb_id'	=> (int) $betrieb_id,
				'menge'			=> (int) $produktion_anzahl,
				'time'			=> (int) time(),
				'status'		=> (int) 0, //0 = in produktion, 1 = abgeschlossen
			));
			$db->sql_query($sql);
			
			add_log('rsp', 0, 'LOG_RSP_RESS_ERSTELLT', $produktion_anzahl, $this->betriebe[$betrieb_id]->name, $this->name, PROVINZ::idToName($this->betriebe[$betrieb_id]->ort));

            //Meldung
            $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
            meta_refresh(5, $meta_url);
            $message = 'Du hast erfolgreich <span style="font-weight:bold">'. $produktion_anzahl .' '. Ressourcen::idToName($this->betriebe[$betrieb_id]->produktion_id) .'</span> in Auftrag gegeben.<br />Die Fertigstellung dauert bis zum nächsten Tag.';
            $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
            trigger_error($message);
		}
        elseif ($this->betriebe[$betrieb_id]->anzahl_produktion == 5)
        {
            //Meldung
            $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
            meta_refresh(5, $meta_url);
            $message = 'Du kannst nicht mehr als 5 Aufträge pro Betrieb gleichzeitig haben!';
            $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
            trigger_error($message);
        }
        //kein Platz mehr in der Fabrik
		else {
            //Meldung
            $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
            meta_refresh(5, $meta_url);
            $message = 'Deine Fabrik kann keine weiteren <span style="font-weight:bold">'. $produktion_anzahl .' '. Ressourcen::idToName($this->betriebe[$betrieb_id]->produktion_id) .'</span> produzieren.<br />Das sprengt ihre maximale Produktion!';
            $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
            trigger_error($message);
			
			add_log('rsp', 0, 'LOG_RSP_RESS_ERWEITERT', $produktion_anzahl, $this->betriebe[$betrieb_id]->name, $this->name, PROVINZ::idToName($this->betriebe[$betrieb_id]->ort));
		}
		

	}
	
	public function genugRohstoffe($betrieb_id,$produktion_anzahl)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		if($this->betriebe[$betrieb_id]->max_produktion < $produktion_anzahl-1)
		{
			return false;
		}
		
		$sql = 'SELECT a.menge AS Rohstoffmenge, b.menge AS Lagermenge, b.ress_id
			FROM ' . RSP_BETRIEBE_ROHSTOFFE_TABLE . ' a
			INNER JOIN ' . RSP_USER_RESS_TABLE . " b ON a.ressourcen_id = b.ress_id
			WHERE a.gebaude_id = ". $this->betriebe[$betrieb_id]->art ."
				and b.user_id = ". $user->data['user_id'];
		$result = $db->sql_query($sql);
		
		while($row = $db->sql_fetchrow($result))
		{
			if( ($row['Rohstoffmenge']*$produktion_anzahl) > $row['Lagermenge'])
			{
				//Meldung
				$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
				meta_refresh(5, $meta_url);
				$message = 'Du hast nicht genug Ressourcen um <span style="font-weight:bold">'. $produktion_anzahl .''. Ressourcen::idToName($row['ress_id']) .'</span> in deinem Betrieb <span style="font-weight:bold">'.  $this->betriebe[$betrieb_id]->name .'</span> herzustellen.<br/> Versuche es mit einer geringeren Mengen.';
				$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
				trigger_error($message);
				return false;
			}
		}
		
		//Hoffentlich ok^^
		//Wenn kein Ergebnis dann gibt es keine Anfroderungen
		$db->sql_freeresult($result);
		return true;
	}
	
	//Gibt es Betrieb?
	//Ist es auch im Güterbereich?
	public function gibtsBetrieb($id)
	{
		global $db;
		
		$sql = 'SELECT id, gueterbereich
			FROM ' . RSP_BETRIEBE_TABLE . "
			WHERE id = $id
			AND (gueterbereich = $this->gueterbereich OR gueterbereich = ". NEUTRALE_GEBAUDE_ID .')';
		$result = $db->sql_query($sql);
		
		if($db->sql_fetchrow($result))
		{
			$db->sql_freeresult($result);
			return true;
		}
		$db->sql_freeresult($result);
		return false;
	}

    public function hatBetriebArt($id)
    {
        foreach($this->betriebe as $value)
        {
            if($id == $value->art)
            {
                return true;
            }
        }
        return false;
    }
	
	//Name sagt alles!
	function gibtsProvinzUndKannBauen($provinz,$betrieb)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		$sql = 'SELECT a.gueterbereich, b.aktuelle_menge, c.land
			FROM ' . RSP_BETRIEBE_TABLE . ' a
			LEFT JOIN ' . RSP_PROVINZ_ROHSTOFF_TABLE . ' b ON b.betrieb_id = a.id
			LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' c ON b.provinz_id = c.id
			WHERE a.id = '. $betrieb .' and 
			(
				(c.id = '. $provinz .' and b.max_menge != 0 and b.aktuelle_menge != 0)
				or 
				a.gueterbereich != 3
			)';
		$result = $db->sql_query($sql);
		//Die Bedinungen sind schon in der SQL-Abfrage enthalten.
		//Wenn es kein Betrieb außerhalb Güterbereich 3 ist, muss die Provinz genug freie Stellen haben.
		if($row = $db->sql_fetchrow($result))
		{
			$db->sql_freeresult($result);
			return true;
		}
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
		meta_refresh(5, $meta_url);
		$message = 'Die Provinz <span style="font-weight:bold">'. PROVINZ::idToName($provinz) .'</span> hat schon die maximale Anzahl an <span style="font-weight:bold">'.  BETRIEB::idToName($betrieb) .'</span>.<br/> Versuche den Betrieb in einer anderen Provinz zu bauen.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
		trigger_error($message);
	}
	
	//Betrieb bauen
	function betriebBauen($betrieb,$provinz,$betrieb_kosten)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;

        //Es darf nur 1 Lager pro Unternehmen gebaut werden!
        if($this->hatBetriebArt(LAGER_ID))
        {
            //Meldung
            $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
            meta_refresh(5, $meta_url);
            $message = 'Dein aktuelles Unternehmen <span style="font-weight:bold">'. $this->name .'</span> hat schon ein Lager. Man darf nur ein Lager pro Unternehmen haben.';
            $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
            trigger_error($message);
        }
        //Maximale Betriebe
        if($this->anzahl_betriebe >= MAX_BETRIEBE)
        {
            //Meldung
            $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
            meta_refresh(5, $meta_url);
            $message = 'Dein aktuelles Unternehmen <span style="font-weight:bold">'. $this->name .'</span> hat schon die maximale Anzahl an Betrieben. Man darf pro Unternehmen nur '. MAX_BETRIEBE .' Betriebe haben.';
            $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
            trigger_error($message);
        }

        add_log('rsp', 0, 'LOG_RSP_NEUER_BETRIEB', BETRIEB::idToName($betrieb), $this->name, PROVINZ::idToName($provinz));

        $sql = 'UPDATE ' . RSP_USER_RESS_TABLE . "
            SET menge = menge-". $betrieb_kosten ."
            WHERE user_id = " . $user->data['user_id'] .'
                and ress_id = 1';
        $db->sql_query($sql);

        $sql = 'INSERT INTO ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . ' ' . $db->sql_build_array('INSERT', array(
            'unternehmen_id'=> (int) $this->unternehmen_id,
            'gebaude_id'	=> (int) $betrieb,
            'provinz_id'	=> (int) $provinz)
        );
        $db->sql_query($sql);

        //Lager erhöht nicht die Betriebsanzahl
        if($betrieb != LAGER_ID)
        {
            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
                SET anzahl_betriebe = anzahl_betriebe+1
                WHERE id = $this->unternehmen_id";
            $db->sql_query($sql);
            $this->anzahl_betriebe++;
        }

        $sql = 'UPDATE ' . RSP_PROVINZ_ROHSTOFF_TABLE . "
            SET aktuelle_menge = aktuelle_menge-1
            WHERE provinz_id = $provinz
                and betrieb_id = $betrieb";
        $db->sql_query($sql);

        //Meldung
        $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
        meta_refresh(5, $meta_url);
        $message = 'Du hast erfolgreich ein Betrieb <span style="font-weight:bold">'. BETRIEB::idToName($betrieb) .'</span> in  der Provinz <span style="font-weight:bold">'.  PROVINZ::idToName($provinz) .'</span> gebaut.';
        $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
        trigger_error($message);
	}
	
	// Betrieb löschen
	function deleteBetrieb($id)
	{
		global $user, $db;
		global $phpbb_root_path, $phpEx;
		
		add_log('rsp', 0, 'LOG_RSP_DELETE_BETRIEB', $this->betriebe[$id]->name, $this->name, PROVINZ::idToName($this->betriebe[$id]->ort));

        //Lager verrringert nicht die Betriebsanzahl
        if($this->betriebe[$id]->art != LAGER_ID)
        {
            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
                SET anzahl_betriebe = anzahl_betriebe-1
                WHERE id = ". $this->unternehmen_id;
            $db->sql_query($sql);
            $this->anzahl_betriebe--;
        }
		
		$sql = 'UPDATE ' . RSP_PROVINZ_ROHSTOFF_TABLE . "
			SET aktuelle_menge = aktuelle_menge+1
			WHERE provinz_id = ". $this->betriebe[$id]->ort ."
				and betrieb_id = ". $this->betriebe[$id]->art;
		$db->sql_query($sql);
		
		$sql = 'DELETE FROM ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . "
				WHERE id = $id";
		$db->sql_query($sql);
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
		meta_refresh(5, $meta_url);
		$message = 'Du hast erfolgreich dein <span style="font-weight:bold">'. $this->betriebe[$id]->name .'</span> in  der Provinz <span style="font-weight:bold">'.  PROVINZ::idToName($this->betriebe[$id]->ort) .'</span> abgerissen.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
		trigger_error($message);
		
		unset($this->betriebe[$id]);
	}
	
	//Unternehmen löschen
	public function deleteUnternehmen($id)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		add_log('rsp', 0, 'LOG_RSP_DELETE_UNTERNEHMEN', $this->name);
		
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_rsp_anzahl_unternehmen = user_rsp_anzahl_unternehmen-1
			WHERE user_id = " . $user->data['user_id'];
		$db->sql_query($sql);
		
		$sql = 'DELETE FROM ' . RSP_UNTERNEHMEN_TABLE . "
				WHERE id = $id";
		$db->sql_query($sql);
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen');
		meta_refresh(5, $meta_url);
		$message = 'Du hast erfolgreich dein Unternehmen <span style="font-weight:bold">'. $this->name .'</span> aufgelöst.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen') . '">', '</a>');
		trigger_error($message);
		
		$this->unternehmen_id = NULL;
		$this->besitzer_id = NULL;
		$this->name = NULL;
		$this->anzahl_betriebe = NULL;
		$this->gueterbereich = NULL;
		$this->betriebe = NULL;
	}

	/**
	 * Unternehmensdaten ändern
	 * @param name
	 * @param logo
	 */
	public function unternehmenAendern()
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
		
		$name	= utf8_normalize_nfc(request_var('unternehmen', ''));
		$logo = BenutzerUnternehmen::logo_process_unternehmen($this->unternehmen_id);
		
		if($name != '' && $logo != FALSE)
		{
			$sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
				SET name = '". (string) htmlspecialchars_decode($name) ."', logo_url = '$logo'
				WHERE id = " . $this->unternehmen_id;
			$db->sql_query($sql);
		}
		elseif ($name != '' && $logo == FALSE) {
			$sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
				SET name = '". (string) htmlspecialchars_decode($name) ."'
				WHERE id = " . $this->unternehmen_id;
			$db->sql_query($sql);
		}
		
		add_log('rsp', 0, 'LOG_RSP_EDIT_UNTERNEHMEN', $this->name, $name);
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen');
		meta_refresh(5, $meta_url);
		$message = 'Du hast erfolgreich dein Unternehmen <span style="font-weight:bold">'. $this->name .'</span> in <span style="font-weight:bold">'. $name .'</span> umbenannt.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen') . '">', '</a>');
		trigger_error($message);
	}
	
	//Erstellt die Liste 'betrieb_block'
	//Dient zum Anzeigen eines Unternehmens und allen Betrieben und nötigen Rohstoffe
	public function listeBetriebe()
	{
		global $template;
		global $phpbb_root_path, $phpEx;
		
		if($this->anzahl_betriebe > 0)
		{
			//Alle Betriebe durchgehen
			foreach($this->betriebe as $value)
			{
				$template->assign_block_vars('betrieb_block', array(
					'ID'				=> $value->gebaude_id,
					'NAME'				=> $value->name,
                    'IMAGE'				=> $value->bild_url,
					'STATUS'			=> $value->aktuelle_produktion,
					'MAX_MENGE'         => ($value->max_produktion - $value->aktuelle_produktion),
					'RESSOURCEN'		=> $value->liste_betrieb_rohstoffe(),
					'MAX_PRODUKTION'	=> $value->max_produktion,
					'LOESCHEN_URL'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;i=$this->unternehmen_id&amp;action=delete&amp;u=".$value->gebaude_id),
				));
                $value->getAuftrag();
			}
		}
	}
	
	//Erstellt eine einfache Liste der Betriebe
	//Wird beim Anzeigen aller Unternehmen benötigt
	public function einfacheListeBetriebe()
	{
		global $user;
		global $phpbb_root_path, $phpEx;
		
		$betrieb = "";
		//Alle Betriebe durchgehen
		foreach($this->betriebe as $value)
		{
			$betrieb .= '' . $value->name . '<br />';
		}
		
		return $betrieb;
	}

	public function genugGeldFuerBetrieb($betrieb_id)
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;
	
		$sql = 'SELECT c.kosten_betrieb
			FROM ' . RSP_USER_RESS_TABLE . ' a
			INNER JOIN '. RSP_BETRIEBE_TABLE .' b
			INNER JOIN '. RSP_GUETERBEREICH_TABLE .' c ON c.id = b.gueterbereich
			WHERE a.user_id = '. $user->data['user_id'] .'
				and a.ress_id = 1
				and a.menge >= c.kosten_betrieb
				and b.id = '. $betrieb_id;
		$result = $db->sql_query($sql);
		
		if($row = $db->sql_fetchrow($result))
		{
			return $row['kosten_betrieb'];
		}
		
		//Meldung
		$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
		meta_refresh(5, $meta_url);
		$message = 'Du hast nicht genug Geld um den Betrieb <span style="font-weight:bold">'. BETRIEB::idToName($betrieb_id) .'</span> zu bauen.';
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
		trigger_error($message);
	}
	
	/**
     * Veraltet!!!
	 * Ist der aktuelle Betrieb in Arbeit?
	 * Prüft auch, ob die Menge noch zum Aufstocken reicht
	 * @param betrieb_id
	 * @param menge, die hinzugefügt werden soll
	 * @return true or false
	 */
	public function kannBetriebNochArbeiten($betrieb_id, $menge)
	{
		global $user;
		global $phpbb_root_path, $phpEx;
		
		if($this->betriebe[$betrieb_id]->getStatus() == 1)
		{
			// Ist die menge + aktuelle menge geringer als die maximale Produktion?
			// dann darf er erweitert werden
			if($this->betriebe[$betrieb_id]->max_produktion >= $menge && $menge != $this->betriebe[$betrieb_id]->aktuelle_menge)
				return true;
			else
			{
				//Meldung
				$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
				meta_refresh(5, $meta_url);
				$message = 'Dein Betrieb <span style="font-weight:bold">'. $this->betriebe[$betrieb_id]->name .'</span> ist voll ausgelastet.<br /> Es kann erst Morgen wieder neue Aufträge bekommen.';
				$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
				trigger_error($message);
			}
		}
		else
			return true;
	}
	
	public function arbeitetDerBetrieb($betrieb_id)
	{
		global $user;
		global $phpbb_root_path, $phpEx;
		
		if($this->betriebe[$betrieb_id]->anzahl_produktion != 0)
		{
			//Meldung
			$meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id);
			meta_refresh(5, $meta_url);
			$message = 'Dein Betrieb <span style="font-weight:bold">'. $this->betriebe[$betrieb_id]->name .'</span> produziert noch was.<br /> Du kannst es erst nach Fertigstellung löschen.';
			$message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=unternehmen&amp;i='. $this->unternehmen_id) . '">', '</a>');
			trigger_error($message);
			return true;
		}
		else
			return false;
	}

    public function unternehmenGehoertUser()
    {
        global $user;

        return ($user->data['user_id'] == $this->besitzer_id)? true: false;
    }
		
	public static function idToName($id)
	{
		global $db;
		
		$sql = 'SELECT name
			FROM ' . RSP_UNTERNEHMEN_TABLE . "
			WHERE id = $id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		return $row['name'];
	}
}

?>