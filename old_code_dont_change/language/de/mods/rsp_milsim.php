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

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
    $lang = array();
}
// Merge language entries into the common lang array
$lang = array_merge($lang, array(
    'REKRUTIERUNG'		    => 'Rekrutierung',
    'EINHEITENVERWALTUNG'	=> 'Einheitenverwaltung',
    'ORGANISATION'			=> 'Organisation',
    'MISSIONEN'		    	=> 'Missionen',
    'EINHEITENCHRONIK'      => 'Einheitenchronik',
    'EINHEITEN_ART'         => 'Art der Einheit',
    'EINHEITEN_KOSTEN'      => 'Kosten',
    'EINHEITEN_DAUER'       => 'Dauer',
    'EINHEITEN_ORT'         => 'Ort',
    'EINHEITEN_BEINAMEN'    => 'Beinamen',
));
?>