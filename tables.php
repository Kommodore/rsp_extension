<?php

/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp;

class tables
{
    public static $table = array(
        'betriebe'              => 'rsp_betriebe',
        'betriebe_rohstoffe'    => 'rsp_betriebe_rohstoffe',
		'changelog'				=> 'rsp_changelog',
        'gebaude'               => 'rsp_gebaude_info',
		'haendler'				=> 'rsp_haendler',
		'handel_log'			=> 'rsp_handel_log',
		'land'					=> 'rsp_land',
        'provinzen'             => 'rsp_provinzen',
		'rsp_raenge'            => 'rsp_raenge',
        'ressourcen'            => 'rsp_ressourcen',
        'ressourcen_bereich'    => 'rsp_ressourcen_bereich',
        'unternehmen'           => 'rsp_unternehmen',
        'unternehmen_betriebe'  => 'rsp_unternehmen_betriebe',
        'user_ress'             => 'rsp_user_ress',
    );
}
