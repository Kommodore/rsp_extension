<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 26.03.14
 * Time: 18:10
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class BenutzerUnternehmen2 {
    private $besitzer_id;
    private $besitzer_name;
    private $unternehmen = array();

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

        $sql = 'SELECT a.id, a.name, a.gueterbereich, a.anzahl_betriebe, a.logo_url, b.username
			FROM ' . RSP_UNTERNEHMEN_TABLE . ' a
			LEFT JOIN '. USERS_TABLE .' b ON b.user_id = a.user_id
			WHERE a.user_id = ' . $this->besitzer_id . '
			ORDER BY id ASC';
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result))
        {
            $info = array(
                'id' => $row['id'],
                'besitzer_id' => $this->besitzer_id,
                'benutzer_name' => $row['username'],
                'name' => $row['name'],
                'logo' => $row['logo_url'],
                'anzahl_betriebe' => $row['anzahl_betriebe'],
                'gueterbereich' => $row['gueterbereich']);
            $this->unternehmen[$row['id']] = new Unternehmen2($info);
            $this->besitzer_name = $row['username'];
        }
        $db->sql_freeresult($result);
    }

    public function unternehmenErstellen()
    {
        global $db, $user, $ress;
        global $phpbb_root_path, $phpEx;

        $name	= utf8_normalize_nfc(request_var('unternehmen', ''));
        $gueterbereich = request_var('gueterbereich', 0);

        if($name == '' || $gueterbereich < 1)
            Meldung::ausgabe('mode=unternehmen', 'Nicht genug Informationen um ein Unternehmen zu erstellen!');

        $rohstoff = array( '1' => $this->preisFuerUnternehmen($gueterbereich));
        if($ress->hatUserGenugRess($rohstoff) && $this->kannUserUnternehmenGrunden())
        {
            $ress->userRessAbziehen($rohstoff);

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
            $logo = BenutzerUnternehmen2::logo_process_unternehmen($nextID);
            if($logo != FALSE)
            {
                $sql = 'UPDATE ' . RSP_UNTERNEHMEN_TABLE . "
                    SET logo_url = '$logo'
                    WHERE id = " . $nextID;
                $db->sql_query($sql);
            }

            Meldung::ausgabe('mode=unternehmen', 'Du hast erfolgreich dein Unternehmen <span style="font-weight:bold">'. $name .'</span> erstellt.');
        }
    }

    private function preisFuerUnternehmen($gueterbereich)
    {
        global $db;

        //Kosten abheben
        $sql = 'SELECT kosten_unternehmen
			FROM ' . RSP_GUETERBEREICH_TABLE .'
			WHERE id = '. $gueterbereich;
        $result = $db->sql_query($sql);

        if($row = $db->sql_fetchrow($result))
        {
            return $row['kosten_unternehmen'];
        }
    }

    private function kannUserUnternehmenGrunden()
    {
        global $user;

        if($user->data['user_rsp_anzahl_unternehmen'] < $this->maxUnternehmen())
            return true;
        else
            Meldung::ausgabe('mode=unternehmen', 'Du hast schon die maximale Anzahl an Unternehmen.');
            return false;
    }

    /**
     * Wieviele Unternehmen kann der User haben?
     * Amtsträger = 1
     * Zivil = 3
     */
    private function maxUnternehmen()
    {
        global $user;

        if($user->data['user_rsp_beruf'] == '' || $user->data['user_rsp_beruf'] == 'zivil')
        {
            return MAX_UNTERNEHMEN_ZIVIL;
        }
        else {
            return MAX_UNTERNEHMEN_AMTTRAEGER;
        }
    }

    public function deleteUnternehmen()
    {
        $unternehmen_id	= request_var('u', 0);

        if($unternehmen_id < 1)
            Meldung::ausgabe('mode=unternehmen', 'Zuwenig Informationen um das Unternehmen zu löschen!');

        $this->unternehmen[$unternehmen_id]->deleteUnternehmen();
    }

    //Erstellt den "unternehmen_block"
    public function listeUnternehmen()
    {
        global $template;
        global $phpbb_root_path, $phpEx;

        foreach($this->unternehmen as $value)
        {
            //Ausgeben der Betriebe
            $betrieb = '<li>Hat keinen Betrieb</li>';
            if ($value->getAnzahlBetriebe() != 0)
            {
                $betrieb = $value->einfacheListeBetriebe();
            }

            $template->assign_block_vars('unternehmen_block', array(
                'UNTERNEHMEN_NAME'		=> $value->getName(),
                'UNTERNEHMEN_LOGO'		=> ($value->getLogo() != '') ? ($phpbb_root_path . "download/file.$phpEx?logo=" . $value->getLogo()) : false,
                'UNTERNEHMEN_URL'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;i=". $value->getUnternehmenId()),
                'UNTERNEHMEN_URL_AENDERN'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen&amp;action=edit&amp;i=". $value->getUnternehmenId()),
                'BETRIEBLISTE'			=> $betrieb,
            ));
        }
    }

    private static function logo_process_unternehmen($nextID)
    {
        global $config, $phpbb_root_path, $phpEx, $user;

        /**
        $upload = (file_exists($phpbb_root_path . $config['avatar_path']) && phpbb_is_writable($phpbb_root_path . $config['avatar_path']) && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;

        if (sizeof($error) && $can_upload == FALSE)
        {
            return false;
        }*/

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

    public function benutzerUnternehmenLayout($action, $mode, $error, $max_unternehmen)
    {
        global $template, $user, $phpbb_root_path, $phpEx;

        $user->add_lang('mods/rsp_unternehmen');

        if($action == 'create')
        {
            $template->set_filenames(array('body' => 'rsp_bauen.html'));


            //RSP Navleiste
            $template->assign_block_vars('navlinks', array(
                    'FORUM_NAME'	=> $user->lang['UNTERNEHMEN_ERSTELLEN'],
                    'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
            );

            //Token bauen
            add_form_key('rsp');

            $template->assign_vars(array(
                'UNTERNEHMEN'				=> true, //Ändern!
                'EDIT'						=> FALSE,
                'U_LISTE_GUETERBEREICH'		=> listeGueterbereich(),
                'U_UNTERNEHMEN_ACTION'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=unternehmen"),
            ));
        }
        else
        {
            $template->set_filenames(array('body' => 'rsp_unternehmen.html'));
            //RSP Navleiste
            if($this->besitzer_id == $user->data['user_id'])
                $template->assign_block_vars('navlinks', array(
                        'FORUM_NAME'	=> $user->lang['USER_UNTERNEHMEN'], //$user->lang[''],
                        'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
                );
            else
            {
                $template->assign_block_vars('navlinks', array(
                        'FORUM_NAME'	=> $user->lang['UNTERNEHMEN'] . ' von ' . $this->besitzer_name, //$user->lang[''],
                        'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
                );
            }
            $template->assign_vars(array(
                'S_BETRIEB'					=> false, //Ändern!
                'S_UNTERNEHMEN'				=> true, //Ändern!
                'L_ANZAHL_UNTERNEHMEN'		=> $user->data['user_rsp_anzahl_unternehmen'],
                'S_EIGENES_UNTERNEHMEN'		=> true, //Ändern!!
                'L_MAX_UNTERNEHMEN'         => $max_unternehmen,
                'S_UNTERNEHMEN_AUSBAUFAHIG'	=> ($user->data['user_rsp_anzahl_unternehmen'] < $max_unternehmen)? true:false,
                'UNTERNEHMEN_ERSTELLEN_URL'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&action=create"),
                'ERROR'						=> (sizeof($error)) ? implode('<br />', $error) : '',
                'U_UNTERNEHMEN_ACTION'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"),
            ));
        }

        //Token bauen
        add_form_key('rsp');

        //Ausgabe
        $this->listeUnternehmen();

    }

    public function getBesitzer()
    {
        return $this->besitzer_id;
    }


} 