<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 04.09.14
 * Time: 17:19
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class Log {

    public static function log_betrieb_ausbau()
    {

    }

    /**
     * @param $art
     * @param int $bau_id
     * @param int $handel_id
     * @param int $produktion_id
     */
    public static function add_log($art, $bau_id=NULL, $handel_id=NULL, $produktion_id=NULL)
    {
        global $db, $user;

        $sql = 'INSERT INTO ' . RSP_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
                'art'           => (int) $art,
                'time'          => (int) time(),
                'user_id'       => (int) $user->data['user_id'],
                'bau_id'	    => (int) $bau_id,
                'handel_id'		=> (int) $handel_id,
                'produktion_id' => (int) $produktion_id,
            ));
        $db->sql_query($sql);
    }

    public static function view_log()
    {
        global $db, $user, $template, $config;
        global $phpbb_root_path, $phpEx;

        $user->add_lang('/mods/info_acp_rsp_logs');

        $limit_days = 0;
        $limit = $config['topics_per_page'];
        $start = request_var('start', 0);
        $offset = $start;

        $sql = 'SELECT COUNT(l.id) AS total_entries
			FROM ' . RSP_LOG_TABLE . " l
			WHERE l.user_id = ". $user->data['user_id'] ."
				  AND l.time >= $limit_days";
        $result = $db->sql_query($sql);
        $log_count = (int) $db->sql_fetchfield('total_entries');
        $db->sql_freeresult($result);

        if ($log_count === 0)
        {
            // Save the queries, because there are no logs to display
            // TODO: Ende des Programms
            return 0;
        }

        if ($offset >= $log_count)
        {
            $offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
        }

        $sql = 'SELECT l.art, l.time,
            g.name AS betrieb_name, u.id AS unternehmen_id, u.name AS unternehmen_name, pr.id as provinz_id, pr.name AS provinz_name,
            h.sender_id, user1.username AS senderName, h.empfaenger_id, user2.username AS empfaengerName, h.zweck_text, h.menge AS handelMenge, h.status AS handelStatus, h.sender_menge,
            ress1.name AS ressName, ress2.name AS senderRessName
			FROM ' . RSP_LOG_TABLE . ' l
			LEFT JOIN ' . RSP_BAU_LOG_TABLE . ' bl ON bl.id = l.bau_id
			LEFT JOIN ' . RSP_HANDEL_LOG_TABLE .' h ON h.id = l.handel_id
			LEFT JOIN ' . RSP_PRODUKTIONS_LOG_TABLE . ' p ON p.id = l.produktion_id
			LEFT JOIN ' . RSP_UNTERNEHMEN_BETRIEBE_TABLE . ' ub ON ub.id = bl.unternehmen_gebaude_id OR ub.id = p.betrieb_id
			LEFT JOIN ' . RSP_UNTERNEHMEN_TABLE . ' u ON u.id = ub.unternehmen_id
			LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = ub.betrieb_id OR b.id = bl.alt_betriebe_stufe_id
			LEFT JOIN ' . RSP_GEBAUDE_INFO_TABLE . ' g ON g.id = b.gebaude_id
			LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' pr ON pr.id = ub.provinz_id OR pr.id = bl.alt_provinz_id
			LEFT JOIN ' . RSP_RESSOURCEN_TABLE . ' ress1 ON ress1.id = h.ressource_art
			LEFT JOIN ' . RSP_RESSOURCEN_TABLE . ' ress2 ON ress2.id = h.sender_ress_art
			LEFT JOIN ' . USERS_TABLE . ' user1 ON user1.user_id = h.sender_id
			LEFT JOIN ' . USERS_TABLE . ' user2 ON user2.user_id = h.empfaenger_id
			WHERE l.user_id = '. $user->data['user_id'] .'
			ORDER BY l.time DESC';
        $result = $db->sql_query_limit($sql, $limit, $offset);

        while ($row = $db->sql_fetchrow($result))
        {
            switch($row['art'])
            {
                case RSP_LOG_HAENDLER:
                    $status = ($row['handelStatus'] == 0)? $user->lang['RSP_AKTIV'] : $user->lang['RSP_BEENDET'];
                    $array = array(
                        $status,
                        $row['handelMenge'],
                        $row['ressName'],
                        $row['empfaengerName'],
                        $row['sender_menge'],
                        $row['senderRessName']);
                    $text = vsprintf($user->lang['RSP_LOG_HAENDLER'], $array);
                    break;
                case RSP_LOG_HANDEL:
                    $status = ($row['handelStatus'] == 0)? $user->lang['RSP_AKTIV'] : $user->lang['RSP_BEENDET'];
                    $array = array(
                        $status,
                        $row['senderName'],
                        $row['handelMenge'],
                        $row['ressName'],
                        $row['zweck_text'],
                        $row['empfaengerName']);
                    $text = vsprintf($user->lang['RSP_LOG_HANDEL'], $array);
                break;
                case RSP_LOG_UNTERNEHMEN:
                    //$text = '<strong>'. $user->lang['BAU']. '</strong><br />>> Das ist ein Test.';
                    $array = array(
                        $row['betrieb_name'],
                        append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;i=".$row['unternehmen_id']),
                        $row['unternehmen_name'],
                        append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$row['provinz_id']),
                        $row['provinz_name']);
                    $text = vsprintf($user->lang['RSP_LOG_NEUER_BETRIEB'], $array);
                break;
            }

            $template->assign_block_vars('rsp_log', array(
                'DATE'				=> $user->format_date($row['time']),
                'ART'				=> $user->lang['BAU'],
                'ACTION'  			=> $text,
            ));
        }

        $template->assign_vars(array(
                'S_ON_PAGE'		=> on_page($log_count, $config['topics_per_page'], $start),
                'PAGINATION'	=> generate_pagination(append_sid("{$phpbb_root_path}rsp.$phpEx"), $log_count, $config['topics_per_page'], $start, true),
            )
        );
    }
} 