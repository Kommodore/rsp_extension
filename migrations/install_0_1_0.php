<?php

/**
*
* @package Games Mod for phpBB3.1
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @copyright (c) 2009-2011 Martin Eddy (mods@mecom.com.au)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp_extension\migrations;

class install_0_1_0 extends \phpbb\db\migration\migration
{
	var $rsp_version = '0.1.0';

	public function effectively_installed()
	{
		return isset($this->config['rsp_version']) && version_compare($this->config['rsp_version'], $this->rsp_version, '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.add', array('rsp_version', $this->rsp_version)),
			// All config

			/*
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_GAMES_INDEX')),
			array('module.add', array(
				'acp', 'ACP_GAMES_INDEX', array(
					'module_basename'	=> '\tacitus89\gamesmod\acp\gamesmod_module',
					'modes'				=> array('config', 'management'),
				),
			)),
			//Set UCP-Module
			array('module.add', array('ucp', false, 'UCP_GAMES_INDEX')),
			array('module.add', array(
				'ucp', 'UCP_GAMES_INDEX', array(
					'module_basename'	=> '\tacitus89\gamesmod\ucp\gamesmod_module',
					'modes'				=> array('index', 'add'),
				),
			)),
			*/
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'rsp_bau_log' => array(
					'COLUMNS'	=> array(
						'id'						=> array('UINT', NULL, 'auto_increment'),
						'unternehmen_gebaude_id'	=> array('UINT:10', 0),
						'alt_betriebe_stufe_id'		=> array('UINT:10', NULL),
						'alt_provinz_id'			=> array('UINT:10', NULL),
						'time'						=> array('UINT:10', 0),
						'status'					=> array('BOOL', 0),
					),
					'PRIMARY_KEY'		=> 'id',
				),
				$this->table_prefix . 'rsp_betriebe' => array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
						'gebaude_id'	=> array('UINT:10', 0),
						'stufe'			=> array('UINT:10', 0),
						'wert'			=> array('UINT:10', 0),
						'bild_url'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_betriebe_kosten' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
						'betrieb_id'	=> array('UINT:10', 0),
						'rohstoff_id'	=> array('UINT:10', 1),
						'menge'			=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_betriebe_rohstoffe' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
						'gebaude_id'	=> array('UINT:10', 0),
						'ressourcen_id'	=> array('UINT:10', 1),
						'menge'			=> array('DECIMAL', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_changelog' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
						'time'			=> array('UINT:10', 0),
						'text'			=> array('MTEXT', ''),
						'text_uid'		=> array('VCHAR:8', ''),
						'text_bitfield'	=> array('VCHAR:255', ''),
						'text_options'	=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_einheiten_art' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
						'name'			=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_gebaude_info' => array(
					'COLUMNS' => array(
						'id'				=> array('UINT', NULL, 'auto_increment'),
						'name'				=> array('VCHAR:255', ''),
						'gueterbereich'		=> array('UINT:10', 0),
						'produktion_id'		=> array('UINT:10', 0),
						'max_stufen'		=> array('SINT:5', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_gueterbereich' => array(
					'COLUMNS' => array(
						'id'					=> array('UINT', NULL, 'auto_increment'),
						'name'					=> array('VCHAR:255', ''),
						'kosten_unternehmen'	=> array('UINT:10', 0),
						'kosten_betrieb'		=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_haendler' => array(
					'COLUMNS' => array(
						'ressource_id'			=> array('UINT:10', 0),
						'preis'					=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'ressource_id',
				),
				$this->table_prefix . 'rsp_handel_log' => array(
					'COLUMNS' => array(
						'id'				=> array('UINT', NULL, 'auto_increment'),
						'sender_id'			=> array('UINT', 0),
						'empfaenger_id'		=> array('UINT', 0),
						'zweck_text'		=> array('VCHAR:255', ''),
						'ressource_art'		=> array('UINT:10', 0),
						'menge'				=> array('UINT:10', 0),
						'sender_ress_art'	=> array('UINT:10', NULL),
						'sender_menge'		=> array('UINT:10', 0),
						'time'				=> array('UINT:10', 0),
						'status'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_land' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
						'name'			=> array('VCHAR:255', ''),
						'kurz_name'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_log' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'time'			=> array('UINT:10', 0),
						'user_id'		=> array('UINT', 0),
						'art'			=> array('TINT:1', 0),
						'bau_id'		=> array('UINT:10', 0),
						'handel_id'		=> array('UINT:10', 0),
						'produktion_id'	=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_produktions_log' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'betrieb_id'	=> array('UINT:10', 0),
						'menge'			=> array('UINT:10', 0),
						'time'			=> array('UINT:10', 0),
						'status'		=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_provinzen' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'name'			=> array('VCHAR:255', ''),
						'hstadt'		=> array('VCHAR:255', ''),
						'land'			=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_provinz_rohstoff' => array(
					'COLUMNS' => array(
						'id'				=> array('UINT:10', NULL, 'auto_increment'),
						'provinz_id'		=> array('UINT:10', 0),
						'betrieb_id'		=> array('UINT:10', 0),
						'max_menge'			=> array('UINT:10', 0),
						'aktuelle_menge'	=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_raenge' => array(
					'COLUMNS' => array(
						'id'		=> array('UINT:10', NULL, 'auto_increment'),
						'land'		=> array('UINT:10', 1),
						'stufe'		=> array('UINT:10', 1),
						'beruf'		=> array('VCHAR:3', ''),
						'name'		=> array('VCHAR:255', ''),
						'url'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_ressourcen' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'name'			=> array('VCHAR:255', ''),
						'url'			=> array('VCHAR:255', ''),
						'bereich_id'	=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_ressourcen_bereich' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'name'			=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_story' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'uberschrift'	=> array('VCHAR:255', ''),
						'text'			=> array('TEXT'),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_story_actions' => array(
					'COLUMNS' => array(
						'id'		=> array('UINT:10', NULL, 'auto_increment'),
						'text'		=> array('VCHAR:255', ''),
						'art'		=> array('TINT:1', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_story_options' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'part_id'		=> array('UINT:10', 0),
						'uberschrift'	=> array('VCHAR:255', ''),
						'action_id'		=> array('UINT:10', 0),
						'wert'			=> array('UINT:10', 0),
						'next_part'		=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_story_part' => array(
					'COLUMNS' => array(
						'id'				=> array('UINT:10', NULL, 'auto_increment'),
						'story_id'			=> array('UINT:10', 0),
						'part'				=> array('UINT:10', 0),
						'uberschrift'		=> array('VCHAR:255', ''),
						'text'				=> array('TEXT'),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_story_user' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'story_id'		=> array('UINT:10', 0),
						'part_id'		=> array('UINT:10', 0),
						'user_id'		=> array('UINT', 0),
						'status'		=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_unternehmen' => array(
					'COLUMNS' => array(
						'id'				=> array('UINT:10', NULL, 'auto_increment'),
						'user_id'			=> array('UINT', 0),
						'name'				=> array('VCHAR:255', ''),
						'logo_url'			=> array('VCHAR:255', ''),
						'gueterbereich'		=> array('UINT:10', 0),
						'anzahl_betriebe'	=> array('USINT:5', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_unternehmen_betriebe' => array(
					'COLUMNS' => array(
						'id'					=> array('UINT:10', NULL, 'auto_increment'),
						'unternehmen_id'		=> array('UINT:10', 0),
						'betrieb_id'			=> array('UINT:10', 0),
						'provinz_id'			=> array('UINT:10', 0),
						'aktuelle_produktion'	=> array('UINT:10', 0),
						'anzahl_produktion'		=> array('USINTUSINT:5', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'rsp_user_ress' => array(
					'COLUMNS' => array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'user_id'		=> array('UINT', 0),
						'ress_id'		=> array('UINT:10', 0),
						'menge'			=> array('UINT:10', 0),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),

			'add_columns'	=> array(
				$this->table_prefix . 'users' => array(
					'user_rsp_name'					=> array('VCHAR:255', ''),
					'user_rsp_land_id'				=> array('UINT:10', 0),
					'user_rsp_rang'					=> array('UINT:10', 0),
					'user_rsp_amt'					=> array('UINT:10', 0),
					'user_rsp_anzahl_unternehmen'	=> array('UINT:10', 0),
					'user_rsp_lagergroesse'			=> array('UINT:10', 25000),
					'user_rsp'						=> array('BOOL', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'rsp_bau_log',
				$this->table_prefix . 'rsp_betriebe',
				$this->table_prefix . 'rsp_betriebe_kosten',
				$this->table_prefix . 'rsp_betriebe_rohstoffe',
				$this->table_prefix . 'rsp_changelog',
				$this->table_prefix . 'rsp_einheiten_art',
				$this->table_prefix . 'rsp_gebaude_info',
				$this->table_prefix . 'rsp_gueterbereich',
				$this->table_prefix . 'rsp_haendler',
				$this->table_prefix . 'rsp_handel_log',
				$this->table_prefix . 'rsp_land',
				$this->table_prefix . 'rsp_log',
				$this->table_prefix . 'rsp_produktions_log',
				$this->table_prefix . 'rsp_provinzen',
				$this->table_prefix . 'rsp_provinzen',
				$this->table_prefix . 'rsp_provinz_rohstoff',
				$this->table_prefix . 'rsp_raenge',
				$this->table_prefix . 'rsp_ressourcen',
				$this->table_prefix . 'rsp_ressourcen_bereich',
				$this->table_prefix . 'rsp_story',
				$this->table_prefix . 'rsp_story_actions',
				$this->table_prefix . 'rsp_story_options',
				$this->table_prefix . 'rsp_story_part',
				$this->table_prefix . 'rsp_story_user',
				$this->table_prefix . 'rsp_unternehmen',
				$this->table_prefix . 'rsp_unternehmen_betriebe',
				$this->table_prefix . 'rsp_user_ress',
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'users' => array(
					'user_rsp_name',
					'user_rsp_land_id',
					'user_rsp_rang',
					'user_rsp_amt',
					'user_rsp_anzahl_unternehmen',
					'user_rsp_lagergroesse',
					'user_rsp',
				),
			),
		);
	}
}
