<?php
/** 
*
* @package acp
* @version $Id: acp_rsp.php 278 2012-10-22 19:18:26Z Strategie-Zone.de  $ 
* @copyright (c) 2011 Strategie-Zone.de 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_rsp.' . $phpEx);

//ist es eine Json anfrage?
if(request_var('mode', '') == 'json')
{
	header('Content-Type: application/json; charset=utf-8"');
	jsonFunktion();
	return;
}


foreach (glob("includes/rsp/*_class." . $phpEx) as $filename)
{
    require_once $filename;
}


// Basic parameter data
$mode	= request_var('mode', '');
$action	= request_var('action', '');
$id		= request_var('i', 0);
$user_id= request_var('u', 0);
$submit_betrieb_bauen	= (isset($_POST['gebaude_bauen'])) ? true : false;
$submit_betrieb_ausbauen	= (isset($_POST['gebaude_ausbauen'])) ? true : false;
$submit_betrieb_loeschen	= (isset($_POST['betrieb_loeschen'])) ? true : false;
$submit_unternehmen_erstellen	= (isset($_POST['unternehmen_erstellen'])) ? true : false;
$submit_unternehmen_aendern	= (isset($_POST['unternehmen_aendern'])) ? true : false;
$submit_unternehmen_loeschen	= (isset($_POST['unternehmen_loeschen'])) ? true : false;
$submit_produktion = (isset($_POST['produkt_bauen'])) ? true : false;
$submit_handel	= (isset($_POST['handeln'])) ? true : false;
$submit_handel_haendler	= (isset($_POST['handel_haendler'])) ? true : false;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$user->add_lang('mods/rsp_uebersicht');

// Funktion zur �berpr�fung
// Ob User ein RSP-Spieler ist
if ($user->data['user_rsp'] == 0)
{
	redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
}

// check form - Bauen
if ($submit_betrieb_bauen && !check_form_key('rsp')
|| $submit_betrieb_ausbauen && !check_form_key('rsp')
|| $submit_betrieb_loeschen && !check_form_key('rsp')
|| $submit_unternehmen_erstellen && !check_form_key('rsp')
|| $submit_unternehmen_aendern && !check_form_key('rsp')
|| $submit_unternehmen_loeschen && !check_form_key('rsp')
|| $submit_handel && !check_form_key('rsp')
|| $submit_handel_haendler && !check_form_key('rsp')
|| $submit_produktion && !check_form_key('rsp')
)
{
	$error[] = $user->lang['FORM_INVALID'];
}


//RSP Index
$template->assign_block_vars('navlinks', array(
	'FORUM_NAME'	=> $user->lang['RSP'],
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx"))
);

//Updates von Handel
$update_handel = new Updates('handel');
//Updates von Produktion
$update_produktion = new Updates('produktion');

//Ressourcenanzeige
//Auf allen Seiten vorhanden
$ress = new Ressourcen2();
$ress->ressourcenAusgabe();

/**
 * Wieviele Unternehmen kann der User haben?
 * Amtsträger = 1
 * Zivil = 3
 */
if($user->data['user_rsp_beruf'] == '' || $user->data['user_rsp_beruf'] == 'zivil')
{
	$max_unternehmen = MAX_UNTERNEHMEN_ZIVIL;
}
else {
	$max_unternehmen = MAX_UNTERNEHMEN_AMTTRAEGER;
}


switch ($mode)
{
	case 'handel':
		$template->set_filenames(array('body' => 'rsp_handel.html'));
		//$user->add_lang('mods/rsp_handel');
		
		//RSP Navleiste
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['HANDEL'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx"))
		);
		
		//Keine Fehler bis hier hin? Und will er handeln?
		if(!sizeof($error) && $submit_handel)
		{
			$username	= request_var('username', '', true);
			$zweck	= utf8_normalize_nfc(request_var('zweck', '', true));
			$ress = request_var('ress', 0);
			$menge = request_var('menge', 0);
			echo $username;
			if($username != '' && $username != $user->data['username'] && $zweck != '' && $ress > 0 && $menge > 0)
			{
				$handel = new Handel($username,$zweck,$ress,$menge);
				if($handel->senderGenugRess())
				{
					$handel->handelAbschliessen();
				}
			}
		}

		//Keine Fehler bis hier hin? Und will er mit dem Händler handeln?
		$haendler = new Haendler();
		if(!sizeof($error) && $submit_handel_haendler)
		{
			$ress = request_var('ress', 0);
			$menge = request_var('menge', 0);
			
			if($haendler->senderGenugRess($ress, $menge))
			{
				$haendler->handelAbschliessen($ress, $menge);
			}
		}
		$haendler->angeboteAusgeben();
		
		//Token bauen
		add_form_key('rsp');
		
		//Ressourcenliste erstellen
        $ress->ressListe();
		
		$template->assign_vars(array(
			'U_LISTE_RESSOURCEN'	=> HANDEL::bildeHandelliste(),
			'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
			'U_UNTERNEHMEN_ACTION'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode&amp;i=$id"),
            'SITE_HANDEL'           => true,
		));
	break;
    case 'ranking':
        $template->set_filenames(array('body' => 'rsp_ranking.html'));
        //RSP Navleiste
        $template->assign_block_vars('navlinks', array(
                'FORUM_NAME'	=> $user->lang['RANKING'],
                'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
        );
        if(!isset($id) || $id < 1)
        {
            $id = 1;
        }
        $ranking = new Ranking($id);
        $ranking->getRessList();
        $ranking->getUserList();

        $template->assign_vars(array(
            'L_CHOSEN_RESS_NAME'        => $ress->idToName($id),
            'L_CHOSEN_RESS_IMG'         => $ress->idToImage($id),
            'SITE_RANKING'              => true
        ));
    break;

    case 'unternehmen':
        //Ein Unternehmen oder eine Unternehmensliste?
        if(isset($id) && $id > 0)
        {
            $unternehmen = new Unternehmen2($id, true);

            //Gehört dem User das Unternehmen?
            //Ansonsten darf er nur ein paar Infos sehen
            if($unternehmen->unternehmenGehoertUser())
            {

                /**
                 * Soll eine Ware produziert werden?
                 */
                if(!sizeof($error) && $submit_produktion)
                {
                    $unternehmen->produktion_erteilen();
                }

                /**
                 * Soll ein Betrieb gebaut werden?
                 */
                if(!sizeof($error) && $submit_betrieb_bauen)
                {
                    $unternehmen->bauen();
                }

                /**
                 * Soll ein Betrieb ausgebaut werden?
                 */
                if(!sizeof($error) && $submit_betrieb_ausbauen)
                {
                    $unternehmen->gebaudeAusbauen();
                }

                /**
                 * Soll ein Betrieb gelöscht werden?
                 */
                if(!sizeof($error) && $submit_betrieb_loeschen)
                {
                    $unternehmen->deleteGebaude();
                }

                /**
                 * Soll ein Unternehmen gelöscht werden?
                 */
                if(!sizeof($error) && $submit_unternehmen_loeschen)
                {
                    $unternehmen->deleteUnternehmen();
                }

                /**
                 * Soll ein Unternehmen geändert werden?
                 */
                if(!sizeof($error) && $submit_unternehmen_aendern)
                {
                    $unternehmen->unternehmenAendern();
                }
            }

            $unternehmen->unternehmenLayout($action, $mode, $error);
        }
        else
        {
            if(isset($user_id) && $user_id >0)
            {
                $benutzerUnternehmen = new BenutzerUnternehmen2($user_id);
                $benutzerUnternehmen->benutzerUnternehmenLayout($action, $mode, $error, $max_unternehmen);
            }
            else
            {
                $benutzerUnternehmen = new BenutzerUnternehmen2($user->data['user_id']);

                //Unternehmen erstellen
                //Keine Fehler bis hier hin?
                if(!sizeof($error) && $submit_unternehmen_erstellen)
                {
                    $benutzerUnternehmen->unternehmenErstellen();
                }
                // Soll ein Unternehmen gelöscht werden?
                if(!sizeof($error) && $action == 'delete')
                {
                    $benutzerUnternehmen->deleteUnternehmen();
                }

                $benutzerUnternehmen->benutzerUnternehmenLayout($action, $mode, $error, $max_unternehmen);
            }
        }
        $template->assign_vars(array(
                'SITE_UNTERNEHMEN'     => true,)
        );
    break;
	case 'provinz':
		$template->set_filenames(array('body' => 'rsp_provinz.html'));
		$user->add_lang('mods/rsp_provinz');
		//provinz($id);

        $provinz = new Provinz($id);
        $provinz->toTemplate();

		
		//RSP Navleiste
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['PROVINZ'],
            'SITE_PROVINZ'     => true,
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
		);
	break;
	case 'land':
		$template->set_filenames(array('body' => 'rsp_provinz.html'));
		$user->add_lang('mods/rsp_provinz');
		land($id);
		
		//RSP Navleiste
		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $user->lang['LAND'],
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
		);
	break;
    case 'story':
        $template->set_filenames(array('body' => 'rsp_story.html'));
        $user->add_lang('mods/rsp_story');

        $story = new Story();

        //RSP Navleiste
        $template->assign_block_vars('navlinks', array(
                'FORUM_NAME'	=> $user->lang['STORY'],
                'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"))
        );
    break;
	default:
		//Allgemeine Übersicht für den Spieler
		$template->set_filenames(array('body' => 'rsp_uebersicht.html'));
		$user->add_lang('mods/rsp_provinz');

        //viewRspLog();
        Log::view_log();

		// Welche Unternehmen hat er?
		$benutzerUnternehmen = new BenutzerUnternehmen2($user->data['user_id']);
		//Ausgabe
		$benutzerUnternehmen->listeUnternehmen();
		$template->assign_vars(array(
			'S_BETRIEB'					=> false, //Ändern!
			'S_UNTERNEHMEN'				=> true, //Ändern!
			'L_UNTERNEHMEN_NAME'		=> $benutzerUnternehmen->name,
			'L_ANZAHL_UNTERNEHMEN'		=> $user->data['user_rsp_anzahl_unternehmen'],
			'L_MAX_UNTERNEHMEN'			=> $max_unternehmen,
			'S_EIGENES_UNTERNEHMEN'		=> true, //Ändern!!
			'S_UNTERNEHMEN_AUSBAUFAHIG'	=> ($user->data['user_rsp_anzahl_unternehmen'] < $max_unternehmen)? true:false,
			'U_LISTE_GUETERBEREICH'		=> listeGueterbereich(),
			'ERROR'						=> (sizeof($error)) ? implode('<br />', $error) : '',
			'U_UNTERNEHMEN_ACTION'		=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=$mode"),
		));
		
		
		// Sein Land und dessen Provinzen
		landMitId($user->data['user_rsp_land_id']);
		
		//Token bauen
		add_form_key('rsp');
		
		//Handelsinfo
		//HANDEL::bildeHandelliste();
		
		$template->assign_vars(array(
			'U_LISTE_RESSOURCEN'		=> listeRessourcen(),
            'SITE_UBERSICHT'               => true,
			'S_UCP_ACTION'				=> append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=handel"),
		));
		//handel();
	break;
}

$template->assign_vars(array(
	'L_CHANGELOG_TITLE'			=> $user->lang['L_CHANGELOG_TITLE'],
	'L_RSP_UPDATE'				=> $user->lang['L_RSP_UPDATE'],
	'U_FIND_USERNAME'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=rsp&amp;field=username&amp;select_single=true'),
));

page_header($user->lang['L_CHANGELOG_TITLE'], false);

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

?>