<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 26.03.14
 * Time: 14:47
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}
require_once('gebaude_class.php');

class Betrieb2 extends Gebaude {
    private $produktion_id;
    private $aktuelle_produktion;
    private $max_produktion;
    private $anzahl_produktion;
    private $rohstoff = array();

    //Betrieb mit bekannter ID
    public function __construct($info, $rohstoffe = false)
    {
        $this->id = $info['id'];
        $this->name = $info['name'];
        $this->betrieb_art = $info['betrieb_art'];
        $this->gebaude_art = $info['gebaude_art'];
        $this->ort = $info['ort'];
        $this->ortname = $info['ortname'];
        $this->bild_url = $info['bild_url'];
        $this->produktion_id = $info['produktion_id'];
        $this->aktuelle_produktion = $info['aktuelle_produktion'];
        $this->max_produktion = $info['max_produktion'];
        $this->anzahl_produktion = $info['anzahl_produktion'];
        $this->stufe = $info['stufe'];
        $this->max_stufen = $info['max_stufen'];

        //Den Betriebe werden die Rohstoffe mit einbezogen
        if($rohstoffe == true)
        {
            global $db;

            $sql = 'SELECT a.ressourcen_id, a.menge, r.name, r.url
			FROM ' . RSP_BETRIEBE_ROHSTOFFE_TABLE . ' a
			LEFT JOIN ' . RSP_RESSOURCEN_TABLE . ' r ON r.id = a.ressourcen_id
			WHERE a.gebaude_id = '. $this->gebaude_art .'
			ORDER BY a.menge DESC';
            $result = $db->sql_query($sql);

            while ($row = $db->sql_fetchrow($result))
            {
                $this->rohstoff[$row['ressourcen_id']] = new Waren($row['ressourcen_id'], $row['name'], $row['url'], $row['menge']);
            }
        }
    }

    public function liste_betrieb_rohstoffe()
    {
        $ress = "";
        $zahl = 0;
        if(count($this->rohstoff) > 0)
        {
            foreach($this->rohstoff as $value)
            {
                $ress .= '<dd>'. $value->getName() .' (<span id="rohstoff-'. $this->id .'-'.++$zahl.'">'. $value->getMenge() .'</span>): <span id="rohstoff-'. $this->id .'-'.++$zahl.'">0</span></dd>';
            }
        }
        else
        {
            $ress = '<dd>Keine</dd>';
        }
        return $ress;
    }

    public function produktion_erteilen($produktion_anzahl, $unternehmen_id)
    {
        global $db, $ress;

        if($this->hatFreieProduktion($produktion_anzahl, $unternehmen_id) &&
           $ress->hatUserGenugRess($this->rohstoff, $produktion_anzahl, $unternehmen_id) &&
           $ress->hatUserGenugPlatz($this->produktion_id, $produktion_anzahl))
        {
            $ress->userRessAbziehen($this->rohstoff, $produktion_anzahl);

            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . "
				SET aktuelle_produktion = ". ($this->aktuelle_produktion + $produktion_anzahl) .",
				anzahl_produktion = anzahl_produktion + 1
				WHERE id = $this->id";
            $db->sql_query($sql);

            // Auftrag erteilen
            $sql = 'INSERT INTO ' . RSP_PRODUKTIONS_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
                    'betrieb_id'	=> (int) $this->id,
                    'menge'			=> (int) $produktion_anzahl,
                    'time'			=> (int) time(),
                    'status'		=> (int) 0, //0 = in produktion, 1 = abgeschlossen
                ));
            $db->sql_query($sql);
            $last_id = $db->sql_nextid();

            Log::add_log(RSP_LOG_PRODUKTION, 0, 0, $last_id);
            add_log('rsp', 0, 'LOG_RSP_RESS_ERSTELLT', $produktion_anzahl, $this->name, $this->name, PROVINZ::idToName($this->ort));

            Meldung::ausgabe('mode=unternehmen&amp;i='. $this->unternehmen_id, 'Du hast erfolgreich <span style="font-weight:bold">'. $produktion_anzahl .' '. $ress->idToName($this->produktion_id) .'</span> in Auftrag gegeben.<br />Die Fertigstellung dauert bis zum nächsten Tag.');
        }
    }

    private function hatFreieProduktion($produktion_anzahl, $unternehmen_id)
    {
        global $ress;

        if($this->aktuelle_produktion < $this->max_produktion &&
           ($this->aktuelle_produktion + $produktion_anzahl) <= $this->max_produktion &&
           $this->anzahl_produktion < MAX_PRODUKTIONSSCHLEIFE)
        {
            return true;
        }
        elseif ($this->anzahl_produktion == MAX_PRODUKTIONSSCHLEIFE)
        {
            Meldung::ausgabe('mode=unternehmen&amp;i='. $unternehmen_id, 'Du kannst nicht mehr als 5 Aufträge pro Betrieb gleichzeitig haben!');
        }
        //kein Platz mehr in der Fabrik
        else {
            Meldung::ausgabe('mode=unternehmen&amp;i='. $unternehmen_id, 'Deine Fabrik kann keine weiteren <span style="font-weight:bold">'. $produktion_anzahl .' '. $ress->idToName($this->produktion_id) .'</span> produzieren.<br />Das sprengt ihre maximale Produktion!');
        }
    }

    public function deleteBetrieb($unternehmen_id)
    {
        global $db;

        add_log('rsp', 0, 'LOG_RSP_DELETE_BETRIEB', $this->name, $this->name, PROVINZ::idToName($this->ort));

        //Lager verrringert nicht die Betriebsanzahl
        if($this->art != LAGER_ID)
        {
            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
                    SET anzahl_betriebe = anzahl_betriebe-1
                    WHERE id = ". $unternehmen_id;
            $db->sql_query($sql);

        }

        $sql = 'UPDATE ' . RSP_PROVINZ_ROHSTOFF_TABLE . "
                SET aktuelle_menge = aktuelle_menge+1
                WHERE provinz_id = ". $this->ort ."
                    and betrieb_id = ". $this->art;
        $db->sql_query($sql);

        $sql = 'DELETE FROM ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . "
                    WHERE id = $this->id";
        $db->sql_query($sql);

        $this->id = NULL;
        $tempName = $this->name;
        $this->name = NULL;
        $this->art = NULL;
        $tempOrt = $this->ort;
        $this->ort = NULL;
        $this->bild_url = NULL;
        $this->produktion_id = NULL;
        $this->aktuelle_produktion = NULL;
        $this->max_produktion = NULL;
        $this->anzahl_produktion = NULL;
        $this->rohstoff = NULL;

        Meldung::ausgabe('mode=unternehmen&amp;i='. $unternehmen_id, 'Du hast erfolgreich dein <span style="font-weight:bold">'. $tempName .'</span> in  der Provinz <span style="font-weight:bold">'.  PROVINZ::idToName($tempOrt) .'</span> abgerissen.');
    }

    public function getAuftrag()
    {
        global $db, $template;

        $sql = 'SELECT a.id, a.menge, a.time
			FROM ' . RSP_PRODUKTIONS_LOG_TABLE . ' a
			WHERE a.betrieb_id = ' . $this->id . '
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

    public function getProduktionId()
    {
        return $this->produktion_id;
    }

    public function getAktuelleProduktion()
    {
        return $this->aktuelle_produktion;
    }

    public function getMaxProduktion()
    {
        return $this->max_produktion;
    }

    public function getAnzahlProduktion()
    {
        return $this->anzahl_produktion;
    }

    public static function idToName($id)
    {
        global $db;

        $sql = 'SELECT b.name
			FROM ' . RSP_BETRIEBE_TABLE . ' a
			LEFT JOIN '. RSP_GEBAUDE_INFO_TABLE .' b ON b.id = a.gebaude_id
			WHERE a.id = '. $id;
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);

        return $row['name'];
    }
} 