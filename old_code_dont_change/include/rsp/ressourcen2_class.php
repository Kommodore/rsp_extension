<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 24.03.14
 * Time: 17:59
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class Ressourcen2 {

    private $ress = array();
    private $ressBereich = array();

    public function __construct()
    {
        require_once('waren_class.php');
        $this->RessourcenErstellen();
    }

    private function RessourcenErstellen()
    {
        global $db, $user;

        $sql = 'SELECT a.id, a.name, a.url, a.bereich_id, c.name AS bereich_name, b.menge
			FROM ' . RSP_RESSOURCEN_TABLE . ' a
			LEFT JOIN ' . RSP_USER_RESS_TABLE . ' b ON b.ress_id = a.id
			LEFT JOIN ' . RSP_RESSOURCEN_BEREICH_TABLE . " c ON a.bereich_id = c.id
			WHERE b.user_id = " . $user->data['user_id'];
        $result = $db->sql_query($sql);

        while($row = $db->sql_fetchrow($result))
        {
            if(!is_object($this->ressBereich[$row['bereich_id']]))
                $this->ressBereich[$row['bereich_id']] = new ressBereich($row['bereich_id'], $row['bereich_name']);
            $this->ress[$row['id']] = new Waren($row['id'], $row['name'], $row['url'], $row['menge']);
            $this->ressBereich[$row['bereich_id']]->setRess($this->ress[$row['id']]);
        }

        $db->sql_freeresult($result);
    }

    /**
     * @param $rohstoff = Waren-Array
     * @param int $produktion_anzahl
     * @param bool $unternehmen_id
     * @return bool
     */
    public function hatUserGenugRess($rohstoff, $produktion_anzahl = 1, $unternehmen_id = false)
    {
        foreach($rohstoff as $k => $value)
        {
            if(($value->getMenge() * $produktion_anzahl) > $this->ress[$k]->getMenge())
                Meldung::ausgabe('mode=unternehmen&amp;i='. $unternehmen_id, 'Du hast nicht genug Ressourcen');
        }
        return true;
    }

    public function hatUserGenugPlatz($rohstoff_id, $produktion_anzahl = 1)
    {
        global $user;

        if( ($this->ress[$rohstoff_id]->getMenge() + $produktion_anzahl) > $user->data['user_rsp_lagergroesse'])
        {
            Meldung::ausgabe('mode=unternehmen&amp;i='. $unternehmen_id, 'Du hast nicht genug Platz in deinem Lager. Baue zuerst dein Lager aus, oder verringere deine Produktion.');
        }
        return true;
    }

    /**
     * @param $rohstoff = Waren-Array
     * @param int $produktion_anzahl
     */
    public function userRessAbziehen($rohstoff, $produktion_anzahl = 1)
    {
        global $db, $user;

        //User-Ress anpassen
        foreach($rohstoff as $k => $value)
        {
            $menge = $value->getMenge() * $produktion_anzahl;
            $sql = 'UPDATE ' . RSP_USER_RESS_TABLE . "
				SET menge = menge - $menge
				WHERE user_id = ". $user->data['user_id'] ."
					and ress_id = $k";
            $db->sql_query($sql);

            $this->ress[$k]->reduceMenge($menge);
        }
    }

    public function userLagerAnpassen()
    {
        global $db, $user;

        foreach($this->ress as $k)
        {
            if($k->getMenge() > $user->data['user_rsp_lagergroesse'] &&
               $k->getId() != 1)
            {
                $sql = 'UPDATE ' . RSP_USER_RESS_TABLE . '
                    SET menge = '. $user->data['user_rsp_lagergroesse'] .'
                    WHERE user_id = '. $user->data['user_id'] .'
                        and ress_id = '. $k->getId();
                $db->sql_query($sql);

                $k->setMenge($user->data['user_rsp_lagergroesse']);
            }
        }
    }

    /**
     * Erstellt den templateBlock 'ress_ id _block'
     */
    public function ressourcenAusgabe()
    {
        //Durchläuft alle Bereiche
        foreach($this->ressBereich AS $bereich_id => $bereiche)
        {
            $bereiche->ressToTemplate();
        }
    }

    /**
     * Erzeugt den templateBlock 'ress_block', der alle Ressourcen nach Bereichen gelistet hat.
     */
    public function ressListe()
    {
        //Durchläuft alle Bereiche
        foreach($this->ressBereich AS $bereiche)
        {
            $bereiche->ressToListe();
        }
    }

    /**
     * Gibt anhand des Names der Ressource die Menge, die der Spieler hat zurück
     * @param Ressourcennamen
     * @return Mixed - Menge
     */
    public function ressNachName($name)
    {
        foreach($this->ress AS $ress)
        {
            if($ress->getName() == $name)
            return $ress->getMenge();
        }
        return 0;
    }

    public function idToWare($id)
    {
        return $this->ress[$id];
    }

    public function idToMenge($id)
    {
        return $this->ress[$id]->getMenge();
    }

    /**
     * Gibt die Urladresse des Bildes zurück
     * @param $id - Ressource
     * @return Mixed - Url-Adresse
     */
    public function idToImage($id)
    {
        return $this->ress[$id]->getUrl;
    }

    /**
     * Gibt den Namen der RessourceID zurück
     * @param $id
     * @return Mixed - Name
     */
    public function idToName($id)
    {
        return $this->ress[$id]->getName();
    }

} 