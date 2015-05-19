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

/**
 * Dient zum Updaten von Handels- und Produktionsdaten
 * @param welcher Bereich soll aktualisiert werden
 */
class Updates
{
	
	/**
	 * Konstruktor
	 * @parameter welcher Bereich soll aktualisiert werden
	 */
	public function __construct($mode)
	{
		switch($mode)
		{
			case 'handel':
				$this->handelBeenden();
				break;
			case 'produktion':
				$this->produktionBeenden();
				break;
		}
	}
	
	/**
	 * Schaut alle Handelaufträge, die vor über 1 Stunde eingestellt wurde, nach
	 * und beendet alle austehende davon
	 */
	private function handelBeenden()
	{
		global $db;
		
		$sql = 'SELECT id, sender_id, empfaenger_id, ressource_art, menge, sender_ress_art, sender_menge
			FROM ' . RSP_HANDEL_LOG_TABLE . '
			WHERE time <= '. (time()-(60*60)) .'
				and status = 0
			ORDER BY time ASC';
		$result = $db->sql_query($sql);
		
		while($row = $db->sql_fetchrow($result))
		{
            if($row['sender_ress_art'] != NULL)
            {
                $sql = 'UPDATE ' . RSP_USER_RESS_TABLE . '
                    SET menge = menge+'. $row['sender_menge'] .'
                    WHERE user_id = '. $row['sender_id'] .' and
                        ress_id = '. $row['sender_ress_art'] .'';
                $db->sql_query($sql);
            }

            $sql = 'UPDATE ' . RSP_USER_RESS_TABLE . '
                SET menge = menge+'. $row['menge'] .'
                WHERE user_id = '. $row['empfaenger_id'] .' and
                    ress_id = '. $row['ressource_art'] .'';
            $db->sql_query($sql);
			
			//Status ändern
			$sql = 'UPDATE ' . RSP_HANDEL_LOG_TABLE . '
				SET status = 1
				WHERE id = '. $row['id'] .'';
			$db->sql_query($sql);
		}
	}
	
	/**
	 * Schaut alle Produktionsaufgaben, die vor heute eingestellt wurden, nach
	 * und beendet alle austehende davon
	 */
	private function produktionBeenden()
	{
		global $db;
        $gestern = time() - (24 * 60 * 60);

        //Abfrage, welcher Betrieb vor 24h angefangen hat zu produzieren.
		$sql = 'SELECT id, betrieb_id, menge
			FROM ' . RSP_PRODUKTIONS_LOG_TABLE . '
			WHERE time <= '. $gestern  .'
				AND status = 0
			ORDER BY time ASC';
		$result = $db->sql_query($sql);
		
		while($row = $db->sql_fetchrow($result))
		{
			//Produktionsergebnis einbinden
			$sql = 'UPDATE ' . RSP_USER_RESS_TABLE . ' a
				INNER JOIN '. RSP_UNTERNEHMEN_BETRIEBE_TABLE .' b
				INNER JOIN '. RSP_BETRIEBE_TABLE .' e ON e.id = b.betrieb_id
				INNER JOIN '. RSP_GEBAUDE_INFO_TABLE .' c ON c.id = e.gebaude_id
				INNER JOIN '. RSP_UNTERNEHMEN_TABLE .' d ON d.id = b.unternehmen_id
				SET a.menge = a.menge+'. $row['menge'] .'
				WHERE a.user_id = d.user_id AND
					a.ress_id = c.produktion_id AND
					b.id = '. $row['betrieb_id'] .'';
			$db->sql_query($sql);

			//Status ändern
			$sql = 'UPDATE ' . RSP_PRODUKTIONS_LOG_TABLE . '
				SET status = 1
				WHERE id = '. $row['id'] .'';
			$db->sql_query($sql);

            //Betriebstatus ändern
            $sql = 'UPDATE ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . '
				SET anzahl_produktion = anzahl_produktion - 1,
				aktuelle_produktion = aktuelle_produktion - '. $row['menge'] .'
				WHERE id = '. $row['betrieb_id'];
            $db->sql_query($sql);
		}
	}
}

?>