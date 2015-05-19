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

class Gebaude {
    protected $id;
    protected $name;
    protected $betrieb_art;
    protected $gebaude_art;
    protected $ort;
    protected $ortname;
    protected $bild_url;
    protected $stufe = 0;
    protected $max_stufen;
    private $wert;

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
        $this->stufe = $info['stufe'];
        $this->max_stufen = $info['max_stufen'];
        $this->wert = $info['wert'];
    }

    public function deleteBetrieb($unternehmen_id)
    {
        global $db;

        add_log('rsp', 0, 'LOG_RSP_DELETE_BETRIEB', $this->name, $this->name, PROVINZ::idToName($this->ort));

        //Lager verrringert nicht die Betriebsanzahl
        if($this->gebaude_art != LAGER_ID)
        {
            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
                    SET anzahl_betriebe = anzahl_betriebe-1
                    WHERE id = ". $unternehmen_id;
            $db->sql_query($sql);

        }

        $sql = 'DELETE FROM ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . "
                    WHERE id = $this->id";
        $db->sql_query($sql);

        $this->id = NULL;
        $tempName = $this->name;
        $this->name = NULL;
        $this->art = NULL;
        $tempOrt = $this->ort;
        $this->ort = NULL;
        $this->bild_url = NULL;

        Meldung::ausgabe('mode=unternehmen&amp;i='. $unternehmen_id, 'Du hast erfolgreich dein <span style="font-weight:bold">'. $tempName .'</span> in  der Provinz <span style="font-weight:bold">'.  PROVINZ::idToName($tempOrt) .'</span> abgerissen.');
    }

    public function ausbauKosten()
    {
        global $db;

        $rohstoff = array();
        $sql = 'SELECT c.id, c.name, b.menge
			FROM ' . RSP_BETRIEBE_TABLE . ' a
			LEFT JOIN '. RSP_BETRIEBE_KOSTEN_TABLE .' b ON b.betrieb_id = a.id
			LEFT JOIN '. RSP_RESSOURCEN_TABLE .' c ON c.id = b.rohstoff_id
			WHERE a.gebaude_id = ' . $this->gebaude_art . '
			AND a.stufe = '. ($this->stufe+1) .'
			ORDER BY b.menge DESC';
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result))
        {
            $rohstoff[$row['id']] = new Waren($row['id'], $row['name'], '', $row['menge']);
        }

        return $rohstoff;
    }

    public static function bauKosten($betrieb_id)
    {
        global $db;

        $rohstoff = array();
        $sql = 'SELECT b.menge, c.name, c.id
                FROM '. RSP_BETRIEBE_TABLE .' a
                LEFT JOIN '. RSP_BETRIEBE_KOSTEN_TABLE .' b ON a.id = b.betrieb_id
                LEFT JOIN '. RSP_RESSOURCEN_TABLE .' c ON c.id = b.rohstoff_id
                WHERE a.id = '. $betrieb_id .'
                ORDER BY b.menge DESC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            $rohstoff[$row['id']] = new Waren($row['id'], $row['name'], '', $row['menge']);
        }

        return $rohstoff;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBetriebArt()
    {
        return $this->betrieb_art;
    }

    public function getGebaudeArt()
    {
        return $this->gebaude_art;
    }

    public function getOrt()
    {
        return $this->ort;
    }

    public function getOrtname()
    {
        return $this->ortname;
    }

    public function getBildUrl()
    {
        return $this->bild_url;
    }

    public function getStufe()
    {
        return $this->stufe;
    }

    public function getMaxStufen()
    {
        return $this->max_stufen;
    }

    public function getWert()
    {
        return $this->wert;
    }

    public function ausbauFaehig()
    {
        if($this->stufe < $this->max_stufen)
            return true;
        else
            return false;
    }
} 