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

function listeBetriebe($bereich)
{
	global $db;
	global $template;
	
	$text = '';
	
	$sql = 'SELECT a.id, a.name, b.kosten_betrieb
		FROM ' . RSP_BETRIEBE_TABLE . ' a
		INNER JOIN '. RSP_GUETERBEREICH_TABLE .' b ON b.id = a.gueterbereich
		WHERE a.gueterbereich = '. $bereich .'
		OR a.gueterbereich = '. NEUTRALE_GEBAUDE_ID .'
		ORDER BY a.id ASC';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$text .= '<option value="'. $row['id'] .'">'. $row['name'] .' - '. $row['kosten_betrieb'] .'</option>';
		
		$template->assign_block_vars('betriebe_block', array(
			'ID'				=> $row['id'],
			'NAME'				=> $row['name'],
			'KOSTEN'			=> $row['kosten_betrieb']
		));
	}
	
	$db->sql_freeresult($result);
	
	return $text;
}

function listeRessourcen()
{
	global $db;
	
	$sql = 'SELECT id, name
		FROM ' . RSP_RESSOURCEN_TABLE .'
		ORDER BY id ASC';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$text .= '<option value="'. $row['id'] .'">'. $row['name'] .'</option>';
	}
	
	$db->sql_freeresult($result);
	
	return $text;
	
}

function listeProvinzen($user_id)
{
	global $db;
	
	$text = '';
	
	$sql = 'SELECT b.id, b.name
		FROM ' . USERS_TABLE . ' a
		LEFT JOIN ' . RSP_PROVINZEN_TABLE . " b ON b.land = a.user_rsp_land_id
		WHERE a.user_id = $user_id
		ORDER BY id ASC";
	// Muss noch um Maximale Rohstoffe erweitert werden
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$text .= '<option value="'. $row['id'] .'">'. $row['name'] .'</option>';
	}
	
	$db->sql_freeresult($result);
	
	return $text;
}

function listeGueterbereich()
{
	global $db;
	
	$sql = 'SELECT id, name, kosten_unternehmen
		FROM ' . RSP_GUETERBEREICH_TABLE .'
		ORDER BY id ASC';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$text .= '<option value="'. $row['id'] .'">'. $row['name'] .' - '. $row['kosten_unternehmen'] .' Cr</option>';
	}
	
	$db->sql_freeresult($result);
	
	return $text;	
}

function land($id)
{
	global $db, $template;
	
	switch($id)
	{
		case 0:
			alleLander();
			$template->assign_vars(array(
				'S_ALLE_LANDER'	=> TRUE,
			));
		break;
		case $id >= 1:
			landMitId($id);
		break;
	}
	
	$template->assign_vars(array(
		'S_LAND'		=> TRUE,
	));
}

function provinz($id)
{
	global $db, $template;
	
	switch($id)
	{
		case 0:
			//alleProvinzen();
			$template->assign_vars(array(
				'S_ALLE_PROVINZEN '	=> TRUE,
			));
		break;
		case $id >= 1:
			provinzMitId($id);
		break;
	}
	
	$template->assign_vars(array(
		'S_PROVINZ'		=> TRUE,
		'U_PROVINZ_ID'	=> $id,
	));
}

function alleLander()
{
	global $db, $template;
	global $phpbb_root_path, $phpEx;

	$sql = 'SELECT a.id AS LandId, a.name AS LandName, b.id AS ProvinzId, b.name AS ProvinzName
		FROM ' . RSP_LAND_TABLE . ' a
		LEFT JOIN ' . RSP_PROVINZEN_TABLE . ' b ON b.land = a.id
		WHERE a.id != 1';
	// Muss noch um Maximale Rohstoffe erweitert werden
	$result = $db->sql_query($sql);
	
	$land_id = 0;
	$schalter = FALSE;
	while ($row = $db->sql_fetchrow($result))
	{
		if ($land_id != $row['LandId'])
		{
			$schalter = TRUE;
			$land_id = $row['LandId'];
		} else { $schalter = FALSE; }
		
		$template->assign_block_vars('land_block', array(
			'SCHALTER'		=> $schalter,
			'LAND_NAME'		=> ($schalter == TRUE) ? $row['LandName'] : '',
			'LAND_URL'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=land&amp;i=".$row['LandId']),
			'PROVINZ_NAME'	=> $row['ProvinzName'],
			'PROVINZ_URL'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$row['ProvinzId']),
		));
	}
	
	$db->sql_freeresult($result);
}

function landMitId($land_id)
{
	global $db, $template;
	global $phpbb_root_path, $phpEx;

	$sql = 'SELECT id AS ProvinzId, name AS ProvinzName
		FROM ' . RSP_PROVINZEN_TABLE . '
		WHERE land = '. $land_id ;
	// Muss noch um Maximale Rohstoffe erweitert werden
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('land_block', array(
			'PROVINZ_NAME'	=> $row['ProvinzName'],
			'PROVINZ_URL'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=provinz&amp;i=".$row['ProvinzId']),
		));
	}
	
	$db->sql_freeresult($result);
}

function provinzMitId($provinz_id)
{
	global $db, $template;

	$sql = 'SELECT a.provinz_id AS ProvinzId, b.name AS BetriebName, a.max_menge AS MaxMenge, a.aktuelle_menge AS Akt_Menge
		FROM ' . RSP_PROVINZ_ROHSTOFF_TABLE . ' a
		LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = a.betrieb_id
		WHERE provinz_id = '. $provinz_id ;
	// Muss noch um Maximale Rohstoffe erweitert werden
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('rohstoff_block', array(
			'NAME'		=> $row['BetriebName'],
			'MENGE'		=> $row['MaxMenge'],
			'AKT_MENGE'	=> $row['Akt_Menge'],
		));
	}
	
	$db->sql_freeresult($result);
	
	betriebeInProvinz($provinz_id);
}

function betriebeInProvinz($provinz_id)
{
	global $db, $template;
	global $phpbb_root_path, $phpEx;

	$sql = 'SELECT b.name AS BetriebName, c.name AS UnternehmenName
		FROM ' . RSP_UNTERNEHMEN_GEBAUDE_TABLE . ' a
		LEFT JOIN ' . RSP_BETRIEBE_TABLE . ' b ON b.id = a.gebaude_id
		LEFT JOIN ' . RSP_UNTERNEHMEN_TABLE . ' c ON c.id = a.unternehmen_id
		WHERE provinz_id = '. $provinz_id ;
	// Muss noch um Maximale Rohstoffe erweitert werden
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('betriebe_block', array(
			'NAME'			=> $row['BetriebName'],
			'UNTERNEHMEN'	=> $row['UnternehmenName'],
		));
	}
	
	$db->sql_freeresult($result);
}

function jsonFunktion()
{
	global $db;
		
	$json = '{"provinzInfo":';
	
	//Provinznamen
	//$json .= '"NAME":';
	$id = 0;
	$sql = 'SELECT a.id, a.name, a.hstadt, b.kurz_name, c.aktuelle_menge, c.max_menge, d.name AS ressName
			FROM ' . RSP_PROVINZEN_TABLE .' a
			INNER JOIN ' . RSP_LAND_TABLE . ' b ON b.id = a.land
			INNER JOIN ' . RSP_PROVINZ_ROHSTOFF_TABLE . ' c ON c.provinz_id = a.id
			INNER JOIN ' . RSP_RESSOURCEN_TABLE . ' d ON d.id = c.betrieb_id+1';
	$result = $db->sql_query($sql);
	while($row = $db->sql_fetchrow($result))
	{
		if($id != $row['id'])
		{
			$provinz[$row['id']]['name'] = $row['name'];
			$provinz[$row['id']]['hstadt'] = $row['hstadt'];
			$provinz[$row['id']]['kurz_name'] = $row['kurz_name'];
			$liste[$row['id']] = $row['kurz_name'];
			$id = $row['id'];
		}
		$provinz[$row['id']][$row['ressName']] = array("aktuell" => $row['aktuelle_menge'], "max" => $row['max_menge']);
	}
	$json .= json_encode($provinz).',';

	$json .= '"provinzListe":';
	$json .= json_encode($liste).'';

	echo $json.'}';
}


function viewRspLog()
{
    global $db, $user, $template, $config, $phpbb_root_path, $phpEx;

    $user->add_lang('/mods/info_acp_rsp_logs');

    $limit_days = 0;
    $limit = $config['topics_per_page'];
    $start = request_var('start', 0);
    $offset = $start;

    $sql = 'SELECT COUNT(l.log_id) AS total_entries
			FROM ' . LOG_TABLE . ' l, ' . USERS_TABLE . " u
			WHERE l.log_type = ". LOG_RSP ."
			    AND l.user_id = ". $user->data['user_id'] ."
				AND l.user_id = u.user_id
				AND l.log_time >= $limit_days";
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

    $sql = "SELECT l.*, u.*
    FROM " . LOG_TABLE . " l
    LEFT JOIN " . USERS_TABLE . " u ON l.user_id = u.user_id
    WHERE l.log_type = ". LOG_RSP ."
        AND l.user_id = ". $user->data['user_id'] ."
        AND l.log_time >= $limit_days
    ORDER BY l.log_time DESC";
    $result = $db->sql_query_limit($sql, $limit, $offset);

    $i = 0;
    $log = array();
    while ($row = $db->sql_fetchrow($result))
    {
        $log[$i] = array(
            'id'				=> $row['log_id'],
            'date'				=> $row['log_time'],
            'action'			=> (isset($user->lang[$row['log_operation']])) ? $user->lang[$row['log_operation']] : '{' . ucfirst(str_replace('_', ' ', $row['log_operation'])) . '}',
        );

        if (!empty($row['log_data']))
        {
            $log_data_ary = @unserialize($row['log_data']);
            $log_data_ary = ($log_data_ary === false) ? array() : $log_data_ary;

            if (isset($user->lang[$row['log_operation']]))
            {
                // Check if there are more occurrences of % than arguments, if there are we fill out the arguments array
                // It doesn't matter if we add more arguments than placeholders
                if ((substr_count($log[$i]['action'], '%') - sizeof($log_data_ary)) > 0)
                {
                    $log_data_ary = array_merge($log_data_ary, array_fill(0, substr_count($log[$i]['action'], '%') - sizeof($log_data_ary), ''));
                }

                $log[$i]['action'] = vsprintf($log[$i]['action'], $log_data_ary);

                // If within the admin panel we do not censor text out
                if (defined('IN_ADMIN'))
                {
                    $log[$i]['action'] = bbcode_nl2br($log[$i]['action']);
                }
                else
                {
                    $log[$i]['action'] = bbcode_nl2br(censor_text($log[$i]['action']));
                }
            }
            else if (!empty($log_data_ary))
            {
                $log[$i]['action'] .= '<br />' . implode('', $log_data_ary);
            }

            /* Apply make_clickable... has to be seen if it is for good. :/
            // Seems to be not for the moment, reconsider later...
            $log[$i]['action'] = make_clickable($log[$i]['action']);
            */


        }

        $template->assign_block_vars('rsp_log', array(
                'ACTION'			=> $log[$i]['action'],
                'DATE'				=> $user->format_date($log[$i]['date']),
                'ID'				=> $log[$i]['id'],
            )
        );
        $i++;
    }

    $template->assign_vars(array(
            'S_ON_PAGE'		=> on_page($log_count, $config['topics_per_page'], $start),
            'PAGINATION'	=> generate_pagination(append_sid("{$phpbb_root_path}rsp.$phpEx"), $log_count, $config['topics_per_page'], $start, true),
        )
    );
}











