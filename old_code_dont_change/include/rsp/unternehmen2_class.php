<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 26.03.14
 * Time: 14:54
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class Unternehmen2 {
    private $unternehmen_id;
    private $besitzer_id;
    private $besitzer_name;
    private $name;
    private $logo;
    private $anzahl_betriebe;
    private $gueterbereich;
    private $betriebe = array();
    private $gebaude = array();

    //Unternehmen mit bekannter ID
    public function __construct($info, $rohstoffe = false)
    {
        if(!is_array($info))
        {
            $this->readUnternehmen($info);
        }
        else
        {
            $this->unternehmen_id = $info['id'];
            $this->besitzer_id = $info['besitzer_id'];
            $this->besitzer_name = $info['besitzer_name'];
            $this->name = $info['name'];
            $this->logo = $info['logo'];
            $this->anzahl_betriebe = $info['anzahl_betriebe'];
            $this->gueterbereich = $info['gueterbereich'];
        }

        if($rohstoffe == false)
            $this->readBetriebe();
        else
            $this->readBetriebeMitRohstoff();

        $this->readGebaude();
    }

    private function readUnternehmen($info)
    {
        global $db;

        $sql = 'SELECT a.id, a.user_id, a.name, a.gueterbereich, a.anzahl_betriebe, a.logo_url, b.username
			FROM ' . RSP_UNTERNEHMEN_TABLE . ' a
			LEFT JOIN '. USERS_TABLE .' b ON b.user_id = a.user_id
			WHERE a.id = ' . $info;
        $result = $db->sql_query($sql);

        if($row = $db->sql_fetchrow($result))
        {
            $this->unternehmen_id = $row['id'];
            $this->besitzer_id = $row['user_id'];
            $this->besitzer_name = $row['username'];
            $this->name = $row['name'];
            $this->logo = $row['logo_url'];
            $this->anzahl_betriebe = $row['anzahl_betriebe'];
            $this->gueterbereich = $row['gueterbereich'];
        }
        $db->sql_freeresult($result);
    }

    private function readBetriebe()
    {
        global $db;

        $sql = 'SELECT a.id AS id, a.provinz_id AS ort, a.aktuelle_produktion, a.anzahl_produktion, b.stufe, b.id AS betrieb_art, g.id AS gebaude_art, g.name, b.bild_url, g.produktion_id, b.wert AS max_produktion, g.max_stufen, p.name AS ortname
			FROM ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . ' a
			LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = a.betrieb_id
			LEFT JOIN ' . RSP_GEBAUDE_INFO_TABLE . ' g ON g.id = b.gebaude_id
			LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' p ON p.id = a.provinz_id
			WHERE a.unternehmen_id = ' . $this->unternehmen_id . '
			AND g.gueterbereich <> '. NEUTRALE_GEBAUDE_ID .'
			ORDER BY b.id ASC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {

            $info = $row;
            $this->betriebe[$info['id']] = new Betrieb2($info);
        }
        $db->sql_freeresult($result);
    }

    private function readBetriebeMitRohstoff()
    {
        global $db;

        $sql = 'SELECT a.id, a.provinz_id AS ort, b.stufe, a.aktuelle_produktion, a.anzahl_produktion, b.id AS betrieb_art, g.id AS gebaude_art, g.name, b.bild_url, g.produktion_id, b.wert AS max_produktion, g.max_stufen,
            p.name AS ortname
			FROM ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . ' a
			LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = a.betrieb_id
			LEFT JOIN ' . RSP_GEBAUDE_INFO_TABLE . ' g ON g.id = b.gebaude_id
			LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' p ON p.id = a.provinz_id
			WHERE a.unternehmen_id = ' . $this->unternehmen_id . '
			AND g.gueterbereich <> '. NEUTRALE_GEBAUDE_ID .'
			ORDER BY b.id DESC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            if($this->betriebe[$row['id']] == NULL)
            {
                $info = $row;

                $this->betriebe[$info['id']] = new Betrieb2($info, true);
            }
        }
        $db->sql_freeresult($result);
    }

    private function readGebaude()
    {
        global $db;

        $sql = 'SELECT a.id AS id, a.provinz_id AS ort, b.stufe, b.id AS betrieb_art, g.id AS gebaude_art, g.name, b.bild_url, g.max_stufen, p.name AS ortname, b.wert
			FROM ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . ' a
			LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = a.betrieb_id
			LEFT JOIN ' . RSP_GEBAUDE_INFO_TABLE . ' g ON g.id = b.gebaude_id
			LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' p ON p.id = a.provinz_id
			WHERE a.unternehmen_id = ' . $this->unternehmen_id . '
			AND g.gueterbereich = '. NEUTRALE_GEBAUDE_ID .'
			ORDER BY b.id ASC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {

            $info = $row;
            $this->gebaude[$info['id']] = new Gebaude($info);
        }
        $db->sql_freeresult($result);
    }

    /**
     * Ist der Betrieb im Unternehmen?
     * @param $betrieb_id
     * @return bool
     */
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
        $produktion_anzahl	= request_var('produktion_anzahl', 0);
        $betrieb_id = request_var('betrieb_id', 0);

        if($produktion_anzahl < 1 || $betrieb_id < 1)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Nicht genug Informationen um Produktion zu starten!');

        if($this->betriebe[$betrieb_id] == NULL)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dieser Betrieb gehört nicht deinem Unternehmen an!');
        else
            $this->betriebe[$betrieb_id]->produktion_erteilen($produktion_anzahl, $this->unternehmen_id);
    }

    //Betrieb bauen
    public function bauen()
    {
        global $db, $ress;

        $betrieb	= request_var('betrieb', 0);
        $provinz	= request_var('provinz', 0);

        if($betrieb < 1 || $provinz < 1)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Nicht genug Informationen um Betrieb zu bauen!');

        //Es darf nur 1 Lager pro Unternehmen gebaut werden!
        if($betrieb == LAGER_ID && $this->hatBetriebArt(LAGER_ID))
            MELDUNG::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dein aktuelles Unternehmen <span style="font-weight:bold">'. $this->name .'</span> hat schon ein Lager. Man darf nur ein Lager pro Unternehmen haben.');

        //Maximale Betriebe
        if($this->anzahl_betriebe >= MAX_BETRIEBE)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dein aktuelles Unternehmen <span style="font-weight:bold">'. $this->name .'</span> hat schon die maximale Anzahl an Betrieben. Man darf pro Unternehmen nur '. MAX_BETRIEBE .' Betriebe haben.');

        if(!$this->kannUnternehmenBetriebBauen($betrieb, $provinz))
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'In der angegebenen Provinz kann diese Fabrik nicht gebaut werden!');

        $rohstoff = Gebaude::bauKosten($betrieb);
        if($ress->hatUserGenugRess($rohstoff))
        {
            $ress->userRessAbziehen($rohstoff);

            $sql = 'INSERT INTO ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . ' ' . $db->sql_build_array('INSERT', array(
                        'unternehmen_id'    => (int) $this->unternehmen_id,
                        'betrieb_id'	=> (int) $betrieb,
                        'provinz_id'	    => (int) $provinz)
                );
            $db->sql_query($sql);
            $last_id = $db->sql_nextid();

            $sql = 'INSERT INTO ' . RSP_BAU_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
                'unternehmen_gebaude_id'    => (int) $last_id,
                'time'                      => (int) time(),
                'status'                    => (int) 1,
            ));
            $db->sql_query($sql);
            $last_id = $db->sql_nextid();

            add_log('rsp', 0, 'LOG_RSP_NEUER_BETRIEB', BETRIEB2::idToName($betrieb), $this->name, PROVINZ::idToName($provinz));
            Log::add_log(RSP_LOG_BAU, $last_id, 0, 0);

            $sql = 'SELECT b.gebaude_id, b.wert
			    FROM ' . RSP_BETRIEBE_TABLE . ' b
			    WHERE b.id = ' . $betrieb;
            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);

            //Lager erhöht nicht die Betriebsanzahl
            if($row['gebaude_id'] != LAGER_ID)
            {
                $this->betriebBauen($betrieb, $provinz);
            }
            else
            {
                $this->LagerBauen($row['wert'],0);
            }
        }
    }

    private function betriebBauen($betrieb, $provinz)
    {
        global $db;

        $sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
                    SET anzahl_betriebe = anzahl_betriebe+1
                    WHERE id = $this->unternehmen_id";
        $db->sql_query($sql);
        $this->anzahl_betriebe++;

        $sql = 'UPDATE ' . RSP_PROVINZ_ROHSTOFF_TABLE . "
                SET aktuelle_menge = aktuelle_menge-1
                WHERE provinz_id = $provinz
                    and betrieb_id = $betrieb";
        $db->sql_query($sql);

        Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Du hast erfolgreich ein Betrieb gebaut.');
    }

    private function LagerBauen($neue_menge, $alte_menge)
    {
        global $db, $user;

        //Lagerkapazitaet erhöhen
        $sql = 'UPDATE ' . USERS_TABLE . '
                SET user_rsp_lagergroesse = user_rsp_lagergroesse + '. ($neue_menge - $alte_menge) .'
                WHERE user_id = '. $user->data['user_id'];
        $db->sql_query($sql);
        $user->data['user_rsp_lagergroesse'] = $user->data['user_rsp_lagergroesse']+ ($neue_menge - $alte_menge);

        Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Du hast erfolgreich ein Lager gebaut.');
    }

    //Betrieb ausbauen
    public function gebaudeAusbauen()
    {
        global $db, $ress;

        $betrieb_id	= request_var('betrieb', 0);

        if($betrieb_id < 1)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Nicht genug Informationen um Betrieb auszubauen!');

        if(!$this->gebaude[$betrieb_id])
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dieses Gebäude existiert nicht in diesem Unternehmen!');

        if(!$this->gebaude[$betrieb_id]->ausbauFaehig())
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dieses Gebäude kann nicht weiter ausgebaut werden!');

        $rohstoff = $this->gebaude[$betrieb_id]->ausbauKosten();
        if($ress->hatUserGenugRess($rohstoff))
        {
            add_log('rsp', 0, 'LOG_RSP_AUSBAU_BETRIEB', $this->gebaude[$betrieb_id]->getName(), $this->name, '');

            $ress->userRessAbziehen($rohstoff);

            $sql = 'SELECT b.id, b.wert
			    FROM ' . RSP_BETRIEBE_TABLE . ' b
			    WHERE b.gebaude_id = ' . $this->gebaude[$betrieb_id]->getGebaudeArt() . '
			    AND b.stufe = '. ($this->gebaude[$betrieb_id]->getStufe()+1);
            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);

            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . '
                    SET betrieb_id = '. $row['id'] .'
                    WHERE id = '. $betrieb_id;
            $db->sql_query($sql);

            $sql = 'INSERT INTO ' . RSP_BAU_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
                    'unternehmen_gebaude_id'    => (int) $betrieb_id,
                    'time'                      => (int) time(),
                    'status'                    => (int) 1,
                ));
            $db->sql_query($sql);
            $last_id = $db->sql_nextid();

            //Lager erhöht die maximale Ressanzahl
            if($this->gebaude[$betrieb_id]->getGebaudeArt() == LAGER_ID)
            {
                $this->LagerBauen($row['wert'], $this->gebaude[$betrieb_id]->getWert());
            }

            Log::add_log(RSP_LOG_BAU, $last_id, 0, 0);
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Gebaude ist erfolgreich ausgebaut worden!');
        }
    }

    public function hatBetriebArt($id)
    {
        foreach($this->betriebe as $value)
        {
            if($id == $value->getGebaudeArt())
            {
                return true;
            }
        }
        return false;
    }

    private function kannUnternehmenBetriebBauen($betrieb, $provinz)
    {
        global $db;

        $sql = 'SELECT g.gueterbereich, b.aktuelle_menge, c.land
			FROM ' . RSP_BETRIEBE_TABLE . ' a
			LEFT JOIN ' . RSP_GEBAUDE_INFO_TABLE . ' g ON g.id = a.gebaude_id
			LEFT JOIN ' . RSP_PROVINZ_ROHSTOFF_TABLE . ' b ON b.betrieb_id = a.id
			LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' c ON b.provinz_id = c.id
			WHERE a.id = '. $betrieb ."
			AND (g.gueterbereich = $this->gueterbereich OR g.gueterbereich = ". NEUTRALE_GEBAUDE_ID .')
			AND
			(
				(c.id = '. $provinz .' and b.max_menge != 0 and b.aktuelle_menge != 0)
				OR
				g.gueterbereich != 3
			)';
        $result = $db->sql_query($sql);

        if($db->sql_fetchrow($result))
        {
            $db->sql_freeresult($result);
            return true;
        }
        $db->sql_freeresult($result);
        return false;
    }

    // Betrieb löschen
    function deleteGebaude()
    {
        $id	= request_var('betrieb_id', 0);

        if($id < 1)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Nicht genug Informationen um Betrieb zu löschen!');

        if(!$this->betriebe[$id] == NULL)
        {
            if($this->betriebe[$id]->getAnzahlProduktion()!=0)
                Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dieser Betrieb produziert noch etwas!<br /> Es kann erst abgerissen werden, wenn es nichts mehr produziert.');
            else
            {
                if($this->betriebe[$id]->getGebaudeArt() != LAGER_ID)
                    $this->deleteBetrieb($this->betriebe[$id]);
                else
                    $this->deleteLager($this->betriebe[$id]);

            }
        }
        elseif(!$this->gebaude[$id] == NULL)
        {
            if($this->gebaude[$id]->getGebaudeArt() != LAGER_ID)
                $this->deleteBetrieb($this->gebaude[$id]);
            else
                $this->deleteLager($this->gebaude[$id]);
        }
        else{
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Dieser Betrieb gehört nicht dem Unternehemen '. $this->name .' an.');
        }


    }

    private function deleteBetrieb($id)
    {
        $this->anzahl_betriebe--;

        $id->deleteBetrieb($this->unternehmen_id);
        unset($id);

        Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Du hast erfolgreich ein Betrieb abgerissen.');
    }

    private function deleteLager($lager)
    {
        global $db, $user, $ress;

        //Lagerkapazitaet verringern
        $sql = 'UPDATE ' . USERS_TABLE . '
                SET user_rsp_lagergroesse = user_rsp_lagergroesse - '. $lager->getWert() .'
                WHERE user_id = '. $user->data['user_id'];
        $db->sql_query($sql);
        $user->data['user_rsp_lagergroesse'] = MAX_RESS;

        $ress->userLagerAnpassen();

        $lager->deleteBetrieb($this->unternehmen_id);
        unset($lager);
        Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Du hast erfolgreich ein Lager abgerissen.');
    }

    //Unternehmen löschen
    public function deleteUnternehmen()
    {
        global $db, $user;

        if($this->besitzer_id != $user->data['user_id'])
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Das Unternehmen '. $this->name .' gehört Dir nicht und kann deswegen auch nicht von Dir gelöscht werden.');
        elseif($this->anzahl_betriebe != 0)
            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Das Unternehmen '. $this->name .' besitzt noch Betriebe! <br />Du kannst er ein Unternehmen löschen, wenn alle Betriebe gelöscht sind.');
        else
        {
            add_log('rsp', 0, 'LOG_RSP_DELETE_UNTERNEHMEN', $this->name);

            $sql = 'UPDATE ' . USERS_TABLE . "
                SET user_rsp_anzahl_unternehmen = user_rsp_anzahl_unternehmen-1
                WHERE user_id = " . $user->data['user_id'];
            $db->sql_query($sql);

            $sql = 'DELETE FROM ' . RSP_UNTERNEHMEN_TABLE . "
                    WHERE id = $this->unternehmen_id";
            $db->sql_query($sql);

            //Meldung
            Meldung::ausgabe('mode=unternehmen', 'Du hast erfolgreich dein Unternehmen <span style="font-weight:bold">'. $this->name .'</span> aufgelöst.');

            $this->unternehmen_id = NULL;
            $this->besitzer_id = NULL;
            $this->name = NULL;
            $this->anzahl_betriebe = NULL;
            $this->gueterbereich = NULL;
            $this->betriebe = NULL;
        }
    }

    /**
     * Unternehmensdaten ändern
     * @param name
     * @param logo
     */
    public function unternehmenAendern()
    {
        global $db, $user;

        if($this->besitzer_id != $user->data['user_id'])
            Meldung::ausgabe('mode=unternehmen', "Das Unternehmen $this->name gehört Dir nicht. Somit kannst du es auch nicht ändern!");
        else
        {
            $name = utf8_normalize_nfc(request_var('unternehmen', ''));
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
            Meldung::ausgabe('mode=unternehmen', 'Du hast erfolgreich dein Unternehmen <span style="font-weight:bold">'. $this->name .'</span> in <span style="font-weight:bold">'. $name .'</span> umbenannt.');
            $this->name = $name;
        }
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
                    'ID'				=> $value->getId(),
                    'NAME'				=> $value->getName(),
                    'IMAGE'				=> $value->getBildUrl(),
                    'STUFE'             => $value->getStufe(),
                    'ORT'				=> $value->getOrtname(),
                    'ORT_URL'           => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$value->getOrt()),
                    'STATUS'			=> $value->getAktuelleProduktion(),
                    'MAX_MENGE'         => ($value->getMaxProduktion() - $value->getAktuelleProduktion()),
                    'RESSOURCEN'		=> $value->liste_betrieb_rohstoffe(),
                    'MAX_PRODUKTION'	=> $value->getMaxProduktion()
                ));
                $value->getAuftrag();
            }
        }
    }

    //Erstellt die Liste 'gebaude_block'
    //Dient zum Anzeigen eines Unternehmens und allen Betrieben und nötigen Rohstoffe
    public function listeGebaude()
    {
        global $template;
        global $phpbb_root_path, $phpEx;

        if(count($this->gebaude) > 0)
        {
            //Alle Betriebe durchgehen
            foreach($this->gebaude as $value)
            {
                $template->assign_block_vars('gebaude_block', array(
                    'ID'				=> $value->getId(),
                    'NAME'				=> $value->getName(),
                    'IMAGE'				=> $value->getBildUrl(),
                    'STUFE'             => $value->getStufe(),
                    'WERT'             => $value->getWert(),
                    'ORT'				=> $value->getOrtname(),
                    'ORT_URL'           => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$value->getOrt()),
                ));
            }
        }
    }

    //Erstellt eine einfache Liste der Betriebe
    //Wird beim Anzeigen aller Unternehmen benötigt
    public function einfacheListeBetriebe()
    {
        global $phpbb_root_path, $phpEx;

        $betrieb = "";
        //Alle Betriebe durchgehen
        foreach($this->betriebe as $value)
        {
            $betrieb .= '<li>'. $value->getName() .' (Stufe '. $value->getStufe() .') in <a href="'. append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$value->getOrt()) .'"> '. $value->getOrtname() .'</a></li>';
        }

        foreach($this->gebaude as $value)
        {
            $betrieb .= '<li>'. $value->getName() .' (Stufe '. $value->getStufe() .') in <a href="'. append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$value->getOrt()) .'"> '. $value->getOrtname() .'</a></li>';
        }

        return $betrieb;
    }

    public function ausbauListe()
    {
        global $template;

        foreach($this->betriebe as $value)
        {
            if($value->ausbauFaehig())
            {
                $kosten = $value->ausbauKosten();
                $kostentext = '';
                foreach($kosten as $k)
                {
                    $kostentext .= $k->getText();
                }
                $template->assign_block_vars('ausbau_block', array(
                    'ID'				=> $value->getId(),
                    'NAME'				=> $value->getName(),
                    'KOSTEN'			=> $kostentext,
                    'STUFE'             => $value->getStufe()+1
                ));
            }
        }

        foreach($this->gebaude as $value)
        {
            if($value->ausbauFaehig())
            {
                $kosten = $value->ausbauKosten();
                $kostentext = '';
                foreach($kosten as $k)
                {
                    $kostentext .= $k->getText();
                }
                $template->assign_block_vars('ausbau_block', array(
                    'ID'				=> $value->getId(),
                    'NAME'				=> $value->getName(),
                    'KOSTEN'			=> $kostentext,
                    'STUFE'             => $value->getStufe()+1
                ));
            }
        }
    }

    private function gebaudeBauListe($bereich)
    {
        global $template, $db;

        $sql = 'SELECT a.id, b.name, b.id AS gebaude_id
		FROM ' . RSP_BETRIEBE_TABLE . ' a
		INNER JOIN '. RSP_GEBAUDE_INFO_TABLE .' b ON b.id = a.gebaude_id
		WHERE a.stufe = 1
		    AND b.gueterbereich = '. $bereich .'
		ORDER BY a.id ASC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            //TODO: WTF! Pro gebäude eine extra sql-abrage?!
            $kosten = Gebaude::bauKosten($row['id']);
            $kostentext = '';
            foreach($kosten as $k)
            {
                $kostentext .= $k->getText();
            }

            if($bereich != NEUTRALE_GEBAUDE_ID)
            {
                $template->assign_block_vars('betriebe_block', array(
                    'ID'				=> $row['id'],
                    'NAME'				=> $row['name'],
                    'KOSTEN'			=> $kostentext,
                ));
            }
            else
            {
                // TODO: Besser machen!
                $lager_vorhanden = false;
                if ($row['gebaude_id'] == LAGER_ID)
                {
                    foreach ($this->gebaude as $value)
                    {
                        if($value->getGebaudeArt() == LAGER_ID)
                            $lager_vorhanden = true;

                    }
                }

                if(!$lager_vorhanden)
                {
                    $template->assign_block_vars('gebaude_bau_block', array(
                        'ID'				=> $row['id'],
                        'NAME'				=> $row['name'],
                        'KOSTEN'			=> $kostentext,
                    ));
                }
            }
        }
    }

    public function unternehmenGehoertUser()
    {
        global $user;

        return ($user->data['user_id'] == $this->besitzer_id)? true: false;
    }

    public function unternehmenLayout($action, $mode, $error)
    {
        global $template, $user, $phpbb_root_path, $phpEx;

        $user->add_lang('mods/rsp_unternehmen');
        if($this->besitzer_id == $user->data['user_id'])
            $template->assign_block_vars('navlinks', array(
                    'FORUM_NAME'	=> $user->lang['USER_UNTERNEHMEN'], //$user->lang[''],
                    'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;u=$this->besitzer_id"))
            );
        else
        {
            $template->assign_block_vars('navlinks', array(
                    'FORUM_NAME'	=> $user->lang['UNTERNEHMEN'] . ' von ' . $this->besitzer_name, //$user->lang[''],
                    'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;u=$this->besitzer_id"))
            );
        }

        $template->assign_block_vars('navlinks', array(
                'FORUM_NAME'	=> $this->name,
                'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;i=$this->unternehmen_id"))
        );

        switch($action)
        {
            case 'edit': $template->set_filenames(array('body' => 'rsp_create_edit_unternehmen.html'));
                $this->unternehmenEditLayout($mode);
                break;
            case 'bauen': $template->set_filenames(array('body' => 'rsp_bauen.html'));
                //TODO: Neues Layoutfunktion!
                $this->unternehmenCreateLayout($mode);
                $this->gebaudeBauListe($this->gueterbereich);
                $this->gebaudeBauListe(NEUTRALE_GEBAUDE_ID);
                $this->ausbauListe();
                $template->assign_vars(array(
                    'UNTERNEHMEN'				=> false,
                    'EDIT'						=> false,
                ));
                break;
            default:
                $template->set_filenames(array('body' => 'rsp_view_unternehmen.html'));
            break;
        }
        $this->unternehmenStandardLayout($mode, $error);
        $this->listeBetriebe();
        $this->listeGebaude();
        //Token bauen
        add_form_key('rsp');
    }

    private function unternehmenCreateLayout($mode)
    {
        global $template, $user, $phpbb_root_path, $phpEx;



        //RSP Navleiste
        $template->assign_block_vars('navlinks', array(
                'FORUM_NAME'	=> $user->lang['UNTERNEHMEN_AENDERN'],
                'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
        );

        $template->assign_vars(array(
            'S_UTNERNEHMEN_LOGO'		=> $phpbb_root_path . "download/file.$phpEx?logo=" .$this->logo,
            'U_UNTERNEHMEN_ACTION'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen"),
            'UNTERNEHMEN_LOESCHBAR'		=> ($this->anzahl_betriebe == 0)? true:false,
            'UNTERNEHMEN_LOESCHEN_URL'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;action=delete&amp;u=".$this->unternehmen_id),
        ));
    }

    private function unternehmenEditLayout($mode)
    {
        global $template, $user, $phpbb_root_path, $phpEx;

        //RSP Navleiste
        $template->assign_block_vars('navlinks', array(
                'FORUM_NAME'	=> $user->lang['UNTERNEHMEN_AENDERN'],
                'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
        );

        $template->assign_vars(array(
            'EDIT'						=> true,
            'S_UNTERNEHMEN_LOGO'		=> $phpbb_root_path . "download/file.$phpEx?logo=" .$this->logo,
            'U_UNTERNEHMEN_ACTION'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen"),
            'UNTERNEHMEN_LOESCHBAR'		=> ($this->anzahl_betriebe == 0)? true:false,
            'UNTERNEHMEN_LOESCHEN_URL'	=> ($this->anzahl_betriebe == 0)? append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;action=delete&amp;u=".$this->unternehmen_id): '',
            'U_BETRIEB_LOESCHEN_ACTION' => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;i=$this->unternehmen_id&amp;action=delete"),
        ));

        if($this->anzahl_betriebe > 0)
        {
            //Alle Betriebe durchgehen
            foreach($this->betriebe as $value)
            {
                $template->assign_block_vars('betriebe_loeschen_block', array(
                    'BETRIEBNAME'				=> $value->getName(),
                    'BETRIEB_ID'                => $value->getId(),
                    'BETRIEB_ORT'               => $value->getOrtname(),
                    'BETRIEB_ORT_URL'           => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$value->getOrt())
                ));
            }
        }

        if(count($this->gebaude) > 0)
        {
            //Alle Betriebe durchgehen
            foreach($this->gebaude as $value)
            {
                $template->assign_block_vars('gebaude_loeschen_block', array(
                    'BETRIEBNAME'				=> $value->getName(),
                    'BETRIEB_ID'                => $value->getId(),
                    'BETRIEB_ORT'               => $value->getOrtname(),
                    'BETRIEB_ORT_URL'           => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$value->getOrt())
                ));
            }
        }
    }

    private function unternehmenStandardLayout($mode,$error)
    {
        global $template, $user, $phpbb_root_path, $phpEx;

        $template->assign_vars(array(
            'U_UNTERNEHMEN_NAME'		=> $this->name,
            'L_ANZAHL_BETRIEBE'			=> $this->anzahl_betriebe,
            'L_MAX_BETRIEBE'			=> MAX_BETRIEBE,
            'S_EIGENES_UNTERNEHMEN'		=> ($this->unternehmenGehoertUser()) ? true : false,
            'S_BETRIEBE_AUSBAUFAHIG'	=> ($this->anzahl_betriebe < MAX_BETRIEBE)? true:false,
            'S_BETRIEBE_AUSBAUEN_URL'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;i=$this->unternehmen_id&amp;action=bauen"),
            'S_UNTERNEHMEN_VERWALTEN_URL'   => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;i=$this->unternehmen_id&amp;action=edit"),
           // 'U_LISTE_BETRIEBE'			=> listeBetriebe($this->gueterbereich),
            'U_LISTE_PROVINZEN'			=> listeProvinzen($user->data['user_id']),
            'ERROR'						=> (sizeof($error)) ? implode('<br />', $error) : '',
            'U_UNTERNEHMEN_ACTION'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;i=$this->unternehmen_id"),
        ));
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

    public function getUnternehmenId()
    {
        return $this->unternehmen_id;
    }

    public function getBesitzerId()
    {
        return $this->besitzer_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function getAnzahlBetriebe()
    {
        return $this->anzahl_betriebe;
    }

    public function getGueterbereich()
    {
        return $this->gueterbereich;
    }

    public function getBetriebe()
    {
        return $this->betriebe;
    }
} 