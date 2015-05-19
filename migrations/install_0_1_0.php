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

	public function update_data()
	{
		return array(
			array('config.add', array('rsp_version', $this->rsp_version)),
			array('custom', array(array($this, 'insert_rsp_betriebe'))),
			array('custom', array(array($this, 'insert_rsp_betriebe_kosten'))),
			array('custom', array(array($this, 'insert_rsp_betriebe_rohstoffe'))),
			array('custom', array(array($this, 'insert_rsp_einheiten_art'))),
			array('custom', array(array($this, 'insert_rsp_gebaude_info'))),
			array('custom', array(array($this, 'insert_rsp_gueterbereich'))),
			array('custom', array(array($this, 'insert_rsp_haendler'))),
			array('custom', array(array($this, 'insert_rsp_land'))),
			array('custom', array(array($this, 'insert_rsp_provinzen'))),
			array('custom', array(array($this, 'insert_rsp_provinz_rohstoff'))),
			array('custom', array(array($this, 'insert_rsp_raenge'))),
			array('custom', array(array($this, 'insert_rsp_ressourcen'))),
			array('custom', array(array($this, 'insert_rsp_ressourcen_bereich'))),
			array('custom', array(array($this, 'insert_rsp_story'))),
			array('custom', array(array($this, 'insert_rsp_story_actions'))),
			array('custom', array(array($this, 'insert_rsp_story_options'))),
			array('custom', array(array($this, 'insert_rsp_story_part'))),

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

	public function insert_rsp_betriebe()
	{
		$rsp_betriebe = array(
			 array('id' => '1','gebaude_id' => '1','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/erz.png'),
			 array('id' => '2','gebaude_id' => '2','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/edelmetall.png'),
			 array('id' => '3','gebaude_id' => '3','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/rohoel.png'),
			 array('id' => '4','gebaude_id' => '4','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/kohle.png'),
			 array('id' => '5','gebaude_id' => '5','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/erdgas.png'),
			 array('id' => '6','gebaude_id' => '6','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/seltene_erden.png'),
			 array('id' => '7','gebaude_id' => '7','stufe' => '1','wert' => '800','bild_url' => 'images/rsp/icon_ress/trinkwasser.png'),
			 array('id' => '8','gebaude_id' => '8','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/holz.png'),
			 array('id' => '9','gebaude_id' => '9','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/zement.png'),
			 array('id' => '10','gebaude_id' => '10','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/landwirtschaft.png'),
			 array('id' => '11','gebaude_id' => '11','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/textilprodukt.png'),
			 array('id' => '12','gebaude_id' => '12','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/nahrungsmittel.png'),
			 array('id' => '13','gebaude_id' => '13','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/militaerische_versorgungsgueter.png'),
			 array('id' => '14','gebaude_id' => '14','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/billige_konsumgüter.png'),
			 array('id' => '15','gebaude_id' => '15','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/tabakwaren.png'),
			 array('id' => '16','gebaude_id' => '17','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/medizinische_feldausruestung.png'),
			 array('id' => '17','gebaude_id' => '18','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/optisches_system.png'),
			 array('id' => '18','gebaude_id' => '19','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/mikroelektronisches_bauteil.png'),
			 array('id' => '19','gebaude_id' => '20','stufe' => '1','wert' => '10','bild_url' => 'images/rsp/icon_ress/regionales_radarsystem.png'),
			 array('id' => '20','gebaude_id' => '21','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/chemiewaffe.png'),
			 array('id' => '21','gebaude_id' => '22','stufe' => '1','wert' => '5','bild_url' => 'images/rsp/icon_ress/drohne.png'),
			 array('id' => '22','gebaude_id' => '23','stufe' => '1','wert' => '5','bild_url' => 'images/rsp/icon_ress/verbessertes_kommunikationssystem.png'),
			 array('id' => '23','gebaude_id' => '24','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/stahl.png'),
			 array('id' => '24','gebaude_id' => '25','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/seltenerd_metall.png'),
			 array('id' => '25','gebaude_id' => '26','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/diesel_treibstoff.png'),
			 array('id' => '26','gebaude_id' => '27','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/chemische_grundstoffe.png'),
			 array('id' => '27','gebaude_id' => '28','stufe' => '1','wert' => '200','bild_url' => 'images/rsp/icon_ress/handwaffenmunition.png'),
			 array('id' => '28','gebaude_id' => '29','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/einfache_technische_bauteile.png'),
			 array('id' => '29','gebaude_id' => '30','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/fortschrittliche_chemische_substanz.png'),
			 array('id' => '30','gebaude_id' => '31','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/schwere_munition_sprengmittel.png'),
			 array('id' => '31','gebaude_id' => '32','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/fortschrittliche_technische_bauteile.png'),
			 array('id' => '32','gebaude_id' => '33','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/transport_und_nutzfahrzeug.png'),
			 array('id' => '33','gebaude_id' => '34','stufe' => '1','wert' => '10','bild_url' => 'images/rsp/icon_ress/lokales_radarsystem.png'),
			 array('id' => '34','gebaude_id' => '35','stufe' => '1','wert' => '5','bild_url' => 'images/rsp/icon_ress/transportflugzeug.png'),
			 array('id' => '35','gebaude_id' => '36','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/infanteriewaffe.png'),
			 array('id' => '36','gebaude_id' => '37','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/artillerie.png'),
			 array('id' => '37','gebaude_id' => '38','stufe' => '1','wert' => '10','bild_url' => 'images/rsp/icon_ress/gepanzertes_fahrzeug.png'),
			 array('id' => '38','gebaude_id' => '39','stufe' => '1','wert' => '10','bild_url' => 'images/rsp/icon_ress/panzer_80er.png'),
			 array('id' => '39','gebaude_id' => '40','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/reaktives_panzerungssystem.png'),
			 array('id' => '40','gebaude_id' => '41','stufe' => '1','wert' => '50','bild_url' => 'images/rsp/icon_ress/infanterie_schutzausruestung.png'),
			 array('id' => '41','gebaude_id' => '42','stufe' => '1','wert' => '25','bild_url' => 'images/rsp/icon_ress/antipanzerlenkwaffe.png'),
			 array('id' => '42','gebaude_id' => '43','stufe' => '1','wert' => '10','bild_url' => 'images/rsp/icon_ress/kurzstreckenrakete.png'),
			 array('id' => '43','gebaude_id' => '45','stufe' => '1','wert' => '25000','bild_url' => 'images/rsp/gebaude/lager_1.png'),
			 array('id' => '44','gebaude_id' => '45','stufe' => '2','wert' => '50000','bild_url' => 'images/rsp/gebaude/lager_2.png'),
			 array('id' => '45','gebaude_id' => '45','stufe' => '3','wert' => '75000','bild_url' => 'images/rsp/gebaude/lager_3.png'),
			 array('id' => '46','gebaude_id' => '45','stufe' => '4','wert' => '100000','bild_url' => 'images/rsp/gebaude/lager_4.png')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_betriebe', $rsp_betriebe);
	}

	public function insert_rsp_betriebe_kosten()
	{
		$rsp_betriebe_kosten = array(
			  array('id' => '1','betrieb_id' => '1','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '2','betrieb_id' => '2','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '3','betrieb_id' => '3','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '4','betrieb_id' => '4','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '5','betrieb_id' => '5','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '6','betrieb_id' => '6','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '7','betrieb_id' => '7','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '8','betrieb_id' => '8','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '9','betrieb_id' => '9','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '10','betrieb_id' => '10','rohstoff_id' => '1','menge' => '25000'),
			  array('id' => '11','betrieb_id' => '11','rohstoff_id' => '1','menge' => '5000'),
			  array('id' => '12','betrieb_id' => '12','rohstoff_id' => '1','menge' => '5000'),
			  array('id' => '13','betrieb_id' => '13','rohstoff_id' => '1','menge' => '5000'),
			  array('id' => '14','betrieb_id' => '14','rohstoff_id' => '1','menge' => '5000'),
			  array('id' => '15','betrieb_id' => '15','rohstoff_id' => '1','menge' => '5000'),
			  array('id' => '16','betrieb_id' => '16','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '17','betrieb_id' => '17','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '18','betrieb_id' => '18','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '19','betrieb_id' => '19','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '20','betrieb_id' => '20','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '21','betrieb_id' => '21','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '22','betrieb_id' => '22','rohstoff_id' => '1','menge' => '250000'),
			  array('id' => '23','betrieb_id' => '23','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '24','betrieb_id' => '24','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '25','betrieb_id' => '25','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '26','betrieb_id' => '26','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '27','betrieb_id' => '27','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '28','betrieb_id' => '28','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '29','betrieb_id' => '29','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '30','betrieb_id' => '30','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '31','betrieb_id' => '31','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '32','betrieb_id' => '32','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '33','betrieb_id' => '33','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '34','betrieb_id' => '34','rohstoff_id' => '1','menge' => '50000'),
			  array('id' => '35','betrieb_id' => '35','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '36','betrieb_id' => '36','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '37','betrieb_id' => '37','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '38','betrieb_id' => '38','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '39','betrieb_id' => '39','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '40','betrieb_id' => '40','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '41','betrieb_id' => '41','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '42','betrieb_id' => '42','rohstoff_id' => '1','menge' => '100000'),
			  array('id' => '43','betrieb_id' => '43','rohstoff_id' => '9','menge' => '1000'),
			  array('id' => '44','betrieb_id' => '43','rohstoff_id' => '10','menge' => '500'),
			  array('id' => '45','betrieb_id' => '44','rohstoff_id' => '9','menge' => '1500'),
			  array('id' => '46','betrieb_id' => '44','rohstoff_id' => '10','menge' => '1000'),
			  array('id' => '47','betrieb_id' => '45','rohstoff_id' => '9','menge' => '2000'),
			  array('id' => '48','betrieb_id' => '45','rohstoff_id' => '10','menge' => '1500'),
			  array('id' => '49','betrieb_id' => '46','rohstoff_id' => '9','menge' => '3000'),
			  array('id' => '50','betrieb_id' => '46','rohstoff_id' => '10','menge' => '2000')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_betriebe_kosten', $rsp_betriebe_kosten);
	}

	public function insert_rsp_betriebe_rohstoffe()
	{
		$rsp_betriebe_rohstoffe = array(
			array('id' => '1','gebaude_id' => '9','ressourcen_id' => '8','menge' => '1'),
			array('id' => '2','gebaude_id' => '10','ressourcen_id' => '8','menge' => '2'),
			array('id' => '3','gebaude_id' => '11','ressourcen_id' => '11','menge' => '2'),
			array('id' => '4','gebaude_id' => '11','ressourcen_id' => '8','menge' => '1'),
			array('id' => '5','gebaude_id' => '24','ressourcen_id' => '2','menge' => '2'),
			array('id' => '6','gebaude_id' => '24','ressourcen_id' => '5','menge' => '2'),
			array('id' => '7','gebaude_id' => '25','ressourcen_id' => '7','menge' => '2'),
			array('id' => '8','gebaude_id' => '25','ressourcen_id' => '5','menge' => '2'),
			array('id' => '9','gebaude_id' => '26','ressourcen_id' => '4','menge' => '2'),
			array('id' => '10','gebaude_id' => '26','ressourcen_id' => '5','menge' => '2'),
			array('id' => '11','gebaude_id' => '27','ressourcen_id' => '8','menge' => '1'),
			array('id' => '12','gebaude_id' => '27','ressourcen_id' => '4','menge' => '1'),
			array('id' => '13','gebaude_id' => '27','ressourcen_id' => '6','menge' => '1'),
			array('id' => '14','gebaude_id' => '12','ressourcen_id' => '11','menge' => '1'),
			array('id' => '15','gebaude_id' => '12','ressourcen_id' => '8','menge' => '1'),
			array('id' => '16','gebaude_id' => '13','ressourcen_id' => '17','menge' => '2'),
			array('id' => '17','gebaude_id' => '13','ressourcen_id' => '8','menge' => '1'),
			array('id' => '18','gebaude_id' => '13','ressourcen_id' => '9','menge' => '1'),
			array('id' => '19','gebaude_id' => '13','ressourcen_id' => '13','menge' => '0.5'),
			array('id' => '20','gebaude_id' => '14','ressourcen_id' => '8','menge' => '1'),
			array('id' => '21','gebaude_id' => '14','ressourcen_id' => '11','menge' => '1'),
			array('id' => '22','gebaude_id' => '14','ressourcen_id' => '9','menge' => '0.5'),
			array('id' => '23','gebaude_id' => '15','ressourcen_id' => '11','menge' => '1'),
			array('id' => '24','gebaude_id' => '15','ressourcen_id' => '9','menge' => '0.5'),
			array('id' => '25','gebaude_id' => '28','ressourcen_id' => '13','menge' => '1'),
			array('id' => '26','gebaude_id' => '28','ressourcen_id' => '16','menge' => '1'),
			array('id' => '27','gebaude_id' => '28','ressourcen_id' => '8','menge' => '1'),
			array('id' => '28','gebaude_id' => '29','ressourcen_id' => '13','menge' => '2'),
			array('id' => '29','gebaude_id' => '29','ressourcen_id' => '16','menge' => '1'),
			array('id' => '30','gebaude_id' => '29','ressourcen_id' => '8','menge' => '1'),
			array('id' => '31','gebaude_id' => '17','ressourcen_id' => '23','menge' => '2'),
			array('id' => '32','gebaude_id' => '17','ressourcen_id' => '8','menge' => '1'),
			array('id' => '33','gebaude_id' => '17','ressourcen_id' => '12','menge' => '1'),
			array('id' => '34','gebaude_id' => '17','ressourcen_id' => '22','menge' => '1'),
			array('id' => '35','gebaude_id' => '18','ressourcen_id' => '26','menge' => '2'),
			array('id' => '36','gebaude_id' => '18','ressourcen_id' => '13','menge' => '2'),
			array('id' => '37','gebaude_id' => '18','ressourcen_id' => '16','menge' => '2'),
			array('id' => '38','gebaude_id' => '19','ressourcen_id' => '14','menge' => '2'),
			array('id' => '39','gebaude_id' => '19','ressourcen_id' => '3','menge' => '2'),
			array('id' => '40','gebaude_id' => '19','ressourcen_id' => '13','menge' => '2'),
			array('id' => '41','gebaude_id' => '19','ressourcen_id' => '26','menge' => '5'),
			array('id' => '42','gebaude_id' => '19','ressourcen_id' => '23','menge' => '4'),
			array('id' => '43','gebaude_id' => '19','ressourcen_id' => '16','menge' => '2'),
			array('id' => '44','gebaude_id' => '20','ressourcen_id' => '26','menge' => '6'),
			array('id' => '45','gebaude_id' => '20','ressourcen_id' => '13','menge' => '4'),
			array('id' => '46','gebaude_id' => '20','ressourcen_id' => '16','menge' => '4'),
			array('id' => '47','gebaude_id' => '20','ressourcen_id' => '23','menge' => '2'),
			array('id' => '48','gebaude_id' => '20','ressourcen_id' => '14','menge' => '2'),
			array('id' => '49','gebaude_id' => '20','ressourcen_id' => '29','menge' => '2'),
			array('id' => '50','gebaude_id' => '20','ressourcen_id' => '36','menge' => '2'),
			array('id' => '51','gebaude_id' => '21','ressourcen_id' => '23','menge' => '6'),
			array('id' => '52','gebaude_id' => '21','ressourcen_id' => '13','menge' => '2'),
			array('id' => '53','gebaude_id' => '21','ressourcen_id' => '26','menge' => '1'),
			array('id' => '54','gebaude_id' => '22','ressourcen_id' => '26','menge' => '6'),
			array('id' => '55','gebaude_id' => '22','ressourcen_id' => '36','menge' => '4'),
			array('id' => '56','gebaude_id' => '22','ressourcen_id' => '29','menge' => '2'),
			array('id' => '57','gebaude_id' => '22','ressourcen_id' => '15','menge' => '1'),
			array('id' => '58','gebaude_id' => '22','ressourcen_id' => '22','menge' => '2'),
			array('id' => '59','gebaude_id' => '22','ressourcen_id' => '23','menge' => '2'),
			array('id' => '60','gebaude_id' => '22','ressourcen_id' => '13','menge' => '1'),
			array('id' => '61','gebaude_id' => '22','ressourcen_id' => '14','menge' => '1'),
			array('id' => '62','gebaude_id' => '22','ressourcen_id' => '3','menge' => '1'),
			array('id' => '63','gebaude_id' => '23','ressourcen_id' => '13','menge' => '2'),
			array('id' => '64','gebaude_id' => '23','ressourcen_id' => '16','menge' => '4'),
			array('id' => '65','gebaude_id' => '23','ressourcen_id' => '23','menge' => '5'),
			array('id' => '66','gebaude_id' => '23','ressourcen_id' => '3','menge' => '1'),
			array('id' => '67','gebaude_id' => '23','ressourcen_id' => '14','menge' => '1'),
			array('id' => '68','gebaude_id' => '23','ressourcen_id' => '36','menge' => '3'),
			array('id' => '69','gebaude_id' => '30','ressourcen_id' => '16','menge' => '2'),
			array('id' => '70','gebaude_id' => '30','ressourcen_id' => '6','menge' => '2'),
			array('id' => '71','gebaude_id' => '30','ressourcen_id' => '8','menge' => '1'),
			array('id' => '72','gebaude_id' => '31','ressourcen_id' => '16','menge' => '3'),
			array('id' => '73','gebaude_id' => '31','ressourcen_id' => '8','menge' => '1'),
			array('id' => '74','gebaude_id' => '31','ressourcen_id' => '13','menge' => '2'),
			array('id' => '75','gebaude_id' => '32','ressourcen_id' => '22','menge' => '2'),
			array('id' => '76','gebaude_id' => '32','ressourcen_id' => '14','menge' => '1'),
			array('id' => '77','gebaude_id' => '32','ressourcen_id' => '3','menge' => '1'),
			array('id' => '78','gebaude_id' => '32','ressourcen_id' => '16','menge' => '1'),
			array('id' => '79','gebaude_id' => '32','ressourcen_id' => '8','menge' => '1'),
			array('id' => '80','gebaude_id' => '33','ressourcen_id' => '13','menge' => '4'),
			array('id' => '81','gebaude_id' => '33','ressourcen_id' => '22','menge' => '2'),
			array('id' => '82','gebaude_id' => '33','ressourcen_id' => '16','menge' => '1'),
			array('id' => '83','gebaude_id' => '33','ressourcen_id' => '9','menge' => '1'),
			array('id' => '84','gebaude_id' => '34','ressourcen_id' => '26','menge' => '5'),
			array('id' => '85','gebaude_id' => '34','ressourcen_id' => '13','menge' => '2'),
			array('id' => '86','gebaude_id' => '34','ressourcen_id' => '16','menge' => '2'),
			array('id' => '87','gebaude_id' => '34','ressourcen_id' => '23','menge' => '1'),
			array('id' => '88','gebaude_id' => '34','ressourcen_id' => '30','menge' => '1'),
			array('id' => '89','gebaude_id' => '35','ressourcen_id' => '13','menge' => '8'),
			array('id' => '90','gebaude_id' => '35','ressourcen_id' => '23','menge' => '6'),
			array('id' => '91','gebaude_id' => '35','ressourcen_id' => '22','menge' => '8'),
			array('id' => '92','gebaude_id' => '35','ressourcen_id' => '26','menge' => '4'),
			array('id' => '93','gebaude_id' => '35','ressourcen_id' => '31','menge' => '1'),
			array('id' => '94','gebaude_id' => '35','ressourcen_id' => '12','menge' => '1'),
			array('id' => '95','gebaude_id' => '35','ressourcen_id' => '36','menge' => '1'),
			array('id' => '96','gebaude_id' => '35','ressourcen_id' => '28','menge' => '1'),
			array('id' => '97','gebaude_id' => '36','ressourcen_id' => '13','menge' => '2'),
			array('id' => '98','gebaude_id' => '36','ressourcen_id' => '9','menge' => '2'),
			array('id' => '99','gebaude_id' => '36','ressourcen_id' => '22','menge' => '1'),
			array('id' => '100','gebaude_id' => '37','ressourcen_id' => '13','menge' => '4'),
			array('id' => '101','gebaude_id' => '37','ressourcen_id' => '22','menge' => '2'),
			array('id' => '102','gebaude_id' => '37','ressourcen_id' => '16','menge' => '1'),
			array('id' => '103','gebaude_id' => '38','ressourcen_id' => '30','menge' => '1'),
			array('id' => '104','gebaude_id' => '38','ressourcen_id' => '13','menge' => '3'),
			array('id' => '105','gebaude_id' => '38','ressourcen_id' => '26','menge' => '1'),
			array('id' => '106','gebaude_id' => '38','ressourcen_id' => '24','menge' => '1'),
			array('id' => '107','gebaude_id' => '38','ressourcen_id' => '29','menge' => '1'),
			array('id' => '108','gebaude_id' => '39','ressourcen_id' => '26','menge' => '3'),
			array('id' => '109','gebaude_id' => '39','ressourcen_id' => '22','menge' => '6'),
			array('id' => '110','gebaude_id' => '39','ressourcen_id' => '3','menge' => '1'),
			array('id' => '111','gebaude_id' => '39','ressourcen_id' => '13','menge' => '16'),
			array('id' => '112','gebaude_id' => '39','ressourcen_id' => '24','menge' => '2'),
			array('id' => '113','gebaude_id' => '39','ressourcen_id' => '29','menge' => '2'),
			array('id' => '114','gebaude_id' => '39','ressourcen_id' => '27','menge' => '1'),
			array('id' => '115','gebaude_id' => '39','ressourcen_id' => '16','menge' => '1'),
			array('id' => '116','gebaude_id' => '40','ressourcen_id' => '13','menge' => '2'),
			array('id' => '117','gebaude_id' => '40','ressourcen_id' => '25','menge' => '2'),
			array('id' => '118','gebaude_id' => '40','ressourcen_id' => '26','menge' => '1'),
			array('id' => '119','gebaude_id' => '40','ressourcen_id' => '16','menge' => '1'),
			array('id' => '120','gebaude_id' => '41','ressourcen_id' => '12','menge' => '2'),
			array('id' => '121','gebaude_id' => '41','ressourcen_id' => '13','menge' => '1'),
			array('id' => '122','gebaude_id' => '41','ressourcen_id' => '22','menge' => '2'),
			array('id' => '123','gebaude_id' => '41','ressourcen_id' => '23','menge' => '2'),
			array('id' => '124','gebaude_id' => '42','ressourcen_id' => '26','menge' => '4'),
			array('id' => '125','gebaude_id' => '42','ressourcen_id' => '36','menge' => '2'),
			array('id' => '126','gebaude_id' => '42','ressourcen_id' => '25','menge' => '2'),
			array('id' => '127','gebaude_id' => '42','ressourcen_id' => '15','menge' => '1'),
			array('id' => '128','gebaude_id' => '42','ressourcen_id' => '13','menge' => '3'),
			array('id' => '129','gebaude_id' => '42','ressourcen_id' => '29','menge' => '1'),
			array('id' => '130','gebaude_id' => '43','ressourcen_id' => '26','menge' => '4'),
			array('id' => '131','gebaude_id' => '43','ressourcen_id' => '36','menge' => '1'),
			array('id' => '132','gebaude_id' => '43','ressourcen_id' => '13','menge' => '3'),
			array('id' => '133','gebaude_id' => '43','ressourcen_id' => '15','menge' => '2'),
			array('id' => '134','gebaude_id' => '43','ressourcen_id' => '25','menge' => '3')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_betriebe_rohstoffe', $rsp_betriebe_rohstoffe);
	}

	public function insert_rsp_einheiten_art()
	{
		$rsp_einheiten_art = array(
			array('id' => '1','name' => 'Infanterie'),
			array('id' => '2','name' => 'Miliz'),
			array('id' => '3','name' => 'mechanisierte Infanterie'),
			array('id' => '4','name' => 'Panzer'),
			array('id' => '5','name' => 'Fallschirmjäger'),
			array('id' => '6','name' => 'Gebirgsjäger'),
			array('id' => '7','name' => 'Panzerartillerie'),
			array('id' => '8','name' => 'mechanisierte Flak'),
			array('id' => '9','name' => 'Pioniere')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_einheiten_art', $rsp_einheiten_art);
	}


	public function insert_rsp_gebaude_info()
	{
		$rsp_gebaude_info = array(
			  array('id' => '1','name' => 'Erz','gueterbereich' => '3','produktion_id' => '2','max_stufen' => '1'),
			  array('id' => '2','name' => 'Edelmetall','gueterbereich' => '3','produktion_id' => '3','max_stufen' => '1'),
			  array('id' => '3','name' => 'Rohöl','gueterbereich' => '3','produktion_id' => '4','max_stufen' => '1'),
			  array('id' => '4','name' => 'Kohle','gueterbereich' => '3','produktion_id' => '5','max_stufen' => '1'),
			  array('id' => '5','name' => 'Erdgas','gueterbereich' => '3','produktion_id' => '6','max_stufen' => '1'),
			  array('id' => '6','name' => 'Seltene Erde','gueterbereich' => '3','produktion_id' => '7','max_stufen' => '1'),
			  array('id' => '7','name' => 'Trinkwasser','gueterbereich' => '3','produktion_id' => '8','max_stufen' => '1'),
			  array('id' => '8','name' => 'Holz','gueterbereich' => '3','produktion_id' => '9','max_stufen' => '1'),
			  array('id' => '9','name' => 'Zement','gueterbereich' => '3','produktion_id' => '10','max_stufen' => '1'),
			  array('id' => '10','name' => 'Landwirtschaft','gueterbereich' => '3','produktion_id' => '11','max_stufen' => '1'),
			  array('id' => '11','name' => 'Textilprodukt','gueterbereich' => '1','produktion_id' => '12','max_stufen' => '1'),
			  array('id' => '12','name' => 'Nahrungsmittel ','gueterbereich' => '1','produktion_id' => '17','max_stufen' => '1'),
			  array('id' => '13','name' => 'Militärische Versorgungsgüter','gueterbereich' => '1','produktion_id' => '18','max_stufen' => '1'),
			  array('id' => '14','name' => 'Billige Konsumgüter','gueterbereich' => '1','produktion_id' => '19','max_stufen' => '1'),
			  array('id' => '15','name' => 'Tabakware','gueterbereich' => '1','produktion_id' => '20','max_stufen' => '1'),
			  array('id' => '17','name' => 'Medizinische Feldausrüstung','gueterbereich' => '2','produktion_id' => '28','max_stufen' => '1'),
			  array('id' => '18','name' => 'Optische Systeme','gueterbereich' => '2','produktion_id' => '29','max_stufen' => '1'),
			  array('id' => '19','name' => 'Mikroelektronische Bauteile','gueterbereich' => '2','produktion_id' => '36','max_stufen' => '1'),
			  array('id' => '20','name' => 'Regionale Radarsysteme','gueterbereich' => '2','produktion_id' => '37','max_stufen' => '1'),
			  array('id' => '21','name' => 'Chemiewaffen','gueterbereich' => '2','produktion_id' => '38','max_stufen' => '1'),
			  array('id' => '22','name' => 'Aufklärungsdrohnen','gueterbereich' => '2','produktion_id' => '41','max_stufen' => '1'),
			  array('id' => '23','name' => 'Verbesserte Kommunikationssysteme','gueterbereich' => '2','produktion_id' => '43','max_stufen' => '1'),
			  array('id' => '24','name' => 'Stahl','gueterbereich' => '4','produktion_id' => '13','max_stufen' => '1'),
			  array('id' => '25','name' => 'Seltenerd-Metalle','gueterbereich' => '4','produktion_id' => '14','max_stufen' => '1'),
			  array('id' => '26','name' => 'Diesel-Treibstoff ','gueterbereich' => '4','produktion_id' => '15','max_stufen' => '1'),
			  array('id' => '27','name' => 'Chemische Grundstoffe','gueterbereich' => '4','produktion_id' => '16','max_stufen' => '1'),
			  array('id' => '28','name' => 'Handwaffen-Munition','gueterbereich' => '4','produktion_id' => '21','max_stufen' => '1'),
			  array('id' => '29','name' => 'Einfache technische Bauteile','gueterbereich' => '4','produktion_id' => '22','max_stufen' => '1'),
			  array('id' => '30','name' => 'Fortschrittliche chemische Substanzen','gueterbereich' => '4','produktion_id' => '23','max_stufen' => '1'),
			  array('id' => '31','name' => 'Schwere Munition','gueterbereich' => '4','produktion_id' => '25','max_stufen' => '1'),
			  array('id' => '32','name' => 'Fortschrittliche technische Bauteile','gueterbereich' => '4','produktion_id' => '26','max_stufen' => '1'),
			  array('id' => '33','name' => 'Transport- und Nutzfahrzeuge','gueterbereich' => '4','produktion_id' => '30','max_stufen' => '1'),
			  array('id' => '34','name' => 'Lokale Radarsysteme','gueterbereich' => '4','produktion_id' => '31','max_stufen' => '1'),
			  array('id' => '35','name' => 'Transportflugzeug','gueterbereich' => '4','produktion_id' => '42','max_stufen' => '1'),
			  array('id' => '36','name' => 'Infanteriewaffen','gueterbereich' => '5','produktion_id' => '24','max_stufen' => '1'),
			  array('id' => '37','name' => 'Artillerie','gueterbereich' => '5','produktion_id' => '27','max_stufen' => '1'),
			  array('id' => '38','name' => 'Gepanzerte Fahrzeuge','gueterbereich' => '5','produktion_id' => '32','max_stufen' => '1'),
			  array('id' => '39','name' => 'Panzer','gueterbereich' => '5','produktion_id' => '33','max_stufen' => '1'),
			  array('id' => '40','name' => 'reaktive Panzerungs-Systeme','gueterbereich' => '5','produktion_id' => '34','max_stufen' => '1'),
			  array('id' => '41','name' => 'Infanterie-Schutzausrüstung','gueterbereich' => '5','produktion_id' => '35','max_stufen' => '1'),
			  array('id' => '42','name' => 'Panzerabwehr-Lenkwaffen','gueterbereich' => '5','produktion_id' => '39','max_stufen' => '1'),
			  array('id' => '43','name' => 'Kurzstrecken-Rakten','gueterbereich' => '5','produktion_id' => '40','max_stufen' => '1'),
			  array('id' => '45','name' => 'Lager','gueterbereich' => '6','produktion_id' => NULL,'max_stufen' => '4')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_gebaude_info', $rsp_gebaude_info);
	}


	public function insert_rsp_gueterbereich()
	{
		$rsp_gueterbereich = array(
			  array('id' => '1','name' => 'Konsumgüter','kosten_unternehmen' => '10000','kosten_betrieb' => '5000'),
			  array('id' => '2','name' => 'Forschungsbetrieb','kosten_unternehmen' => '250000','kosten_betrieb' => '250000'),
			  array('id' => '3','name' => 'Förderbetrieb','kosten_unternehmen' => '50000','kosten_betrieb' => '25000'),
			  array('id' => '4','name' => 'Fabrik','kosten_unternehmen' => '100000','kosten_betrieb' => '50000'),
			  array('id' => '5','name' => 'Militärische Produktion','kosten_unternehmen' => '150000','kosten_betrieb' => '100000'),
			  array('id' => '6','name' => 'Neutrale Gebäude','kosten_unternehmen' => '1000','kosten_betrieb' => '1000')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_gueterbereich', $rsp_gueterbereich);
	}


	public function insert_rsp_haendler()
	{
		$rsp_haendler = array(
			  array('ressource_id' => '2','preis' => '6'),
			  array('ressource_id' => '3','preis' => '70'),
			  array('ressource_id' => '4','preis' => '8'),
			  array('ressource_id' => '5','preis' => '5'),
			  array('ressource_id' => '6','preis' => '7'),
			  array('ressource_id' => '7','preis' => '60'),
			  array('ressource_id' => '8','preis' => '1'),
			  array('ressource_id' => '9','preis' => '4'),
			  array('ressource_id' => '10','preis' => '1'),
			  array('ressource_id' => '11','preis' => '3'),
			  array('ressource_id' => '12','preis' => '8'),
			  array('ressource_id' => '13','preis' => '28'),
			  array('ressource_id' => '14','preis' => '220'),
			  array('ressource_id' => '15','preis' => '45'),
			  array('ressource_id' => '16','preis' => '25'),
			  array('ressource_id' => '17','preis' => '4'),
			  array('ressource_id' => '18','preis' => '15'),
			  array('ressource_id' => '19','preis' => '6'),
			  array('ressource_id' => '20','preis' => '6'),
			  array('ressource_id' => '21','preis' => '67'),
			  array('ressource_id' => '22','preis' => '125'),
			  array('ressource_id' => '23','preis' => '130'),
			  array('ressource_id' => '24','preis' => '300'),
			  array('ressource_id' => '25','preis' => '225'),
			  array('ressource_id' => '26','preis' => '1200'),
			  array('ressource_id' => '27','preis' => '550'),
			  array('ressource_id' => '28','preis' => '575'),
			  array('ressource_id' => '29','preis' => '5000'),
			  array('ressource_id' => '30','preis' => '730'),
			  array('ressource_id' => '31','preis' => '12000'),
			  array('ressource_id' => '32','preis' => '12000'),
			  array('ressource_id' => '33','preis' => '54000'),
			  array('ressource_id' => '34','preis' => '5400'),
			  array('ressource_id' => '35','preis' => '1500'),
			  array('ressource_id' => '36','preis' => '28000'),
			  array('ressource_id' => '37','preis' => '300000'),
			  array('ressource_id' => '38','preis' => '4500'),
			  array('ressource_id' => '39','preis' => '200000'),
			  array('ressource_id' => '40','preis' => '300000'),
			  array('ressource_id' => '41','preis' => '1200000'),
			  array('ressource_id' => '42','preis' => '300000'),
			  array('ressource_id' => '43','preis' => '750000')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_haendler', $rsp_haendler);
	}

	public function insert_rsp_land()
	{
		$rsp_land = array(
			  array('id' => '1','name' => 'Neutral','kurz_name' => 'NEU'),
			  array('id' => '2','name' => 'Freie Republik Tadsowien','kurz_name' => 'FRT'),
			  array('id' => '3','name' => 'Unabhängige Suranische Republik','kurz_name' => 'USR'),
			  array('id' => '4','name' => 'Volksrepublik Bakirien','kurz_name' => 'VRB')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_land', $rsp_land);
	}

	public function insert_rsp_provinzen()
	{
		$rsp_provinzen = array(
			  array('id' => '1','name' => 'Warandi','hstadt' => 'Bukawasch','land' => '4'),
			  array('id' => '2','name' => 'Isoria','hstadt' => 'Nobri','land' => '4'),
			  array('id' => '3','name' => 'Kunrud','hstadt' => 'Deskul','land' => '1'),
			  array('id' => '4','name' => 'Baku','hstadt' => 'Maschkad','land' => '3'),
			  array('id' => '5','name' => 'Agdam','hstadt' => 'Isahwas','land' => '3'),
			  array('id' => '6','name' => 'Tekstov','hstadt' => 'Schirab','land' => '1'),
			  array('id' => '7','name' => 'Aljaria','hstadt' => 'Chakahar','land' => '1'),
			  array('id' => '8','name' => 'Lobow','hstadt' => 'Jvari','land' => '1'),
			  array('id' => '9','name' => 'Karakenda','hstadt' => 'Rhisnak','land' => '1'),
			  array('id' => '10','name' => 'Tscherjna','hstadt' => 'Bolmisi','land' => '1'),
			  array('id' => '11','name' => 'Berelnij','hstadt' => 'Rastuwan','land' => '2'),
			  array('id' => '12','name' => 'Awostnien','hstadt' => 'Tiplis','land' => '2'),
			  array('id' => '13','name' => 'Tarsutska','hstadt' => 'Baschari','land' => '2'),
			  array('id' => '14','name' => 'Krest','hstadt' => 'Ritsa','land' => '1'),
			  array('id' => '15','name' => 'Bakschar','hstadt' => 'Ismaeli','land' => '1'),
			  array('id' => '16','name' => 'Takistan','hstadt' => 'Gernan','land' => '3'),
			  array('id' => '17','name' => 'Dmnasi','hstadt' => 'Surkut','land' => '4')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_provinzen', $rsp_provinzen);
	}

	public function insert_rsp_provinz_rohstoff()
	{
		$rsp_provinz_rohstoff = array(
			  array('id' => '1','provinz_id' => '1','betrieb_id' => '1','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '2','provinz_id' => '1','betrieb_id' => '2','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '3','provinz_id' => '1','betrieb_id' => '3','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '4','provinz_id' => '1','betrieb_id' => '4','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '5','provinz_id' => '1','betrieb_id' => '5','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '6','provinz_id' => '1','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '7','provinz_id' => '1','betrieb_id' => '7','max_menge' => '10','aktuelle_menge' => '10'),
			  array('id' => '8','provinz_id' => '1','betrieb_id' => '8','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '9','provinz_id' => '1','betrieb_id' => '9','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '10','provinz_id' => '1','betrieb_id' => '10','max_menge' => '9','aktuelle_menge' => '9'),
			  array('id' => '11','provinz_id' => '2','betrieb_id' => '1','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '12','provinz_id' => '2','betrieb_id' => '2','max_menge' => '2','aktuelle_menge' => '0'),
			  array('id' => '13','provinz_id' => '2','betrieb_id' => '3','max_menge' => '2','aktuelle_menge' => '0'),
			  array('id' => '14','provinz_id' => '2','betrieb_id' => '4','max_menge' => '3','aktuelle_menge' => '1'),
			  array('id' => '15','provinz_id' => '2','betrieb_id' => '5','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '16','provinz_id' => '2','betrieb_id' => '6','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '17','provinz_id' => '2','betrieb_id' => '7','max_menge' => '7','aktuelle_menge' => '7'),
			  array('id' => '18','provinz_id' => '2','betrieb_id' => '8','max_menge' => '2','aktuelle_menge' => '1'),
			  array('id' => '19','provinz_id' => '2','betrieb_id' => '9','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '20','provinz_id' => '2','betrieb_id' => '10','max_menge' => '7','aktuelle_menge' => '7'),
			  array('id' => '21','provinz_id' => '3','betrieb_id' => '1','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '22','provinz_id' => '3','betrieb_id' => '2','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '23','provinz_id' => '3','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '24','provinz_id' => '3','betrieb_id' => '4','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '25','provinz_id' => '3','betrieb_id' => '5','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '26','provinz_id' => '3','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '27','provinz_id' => '3','betrieb_id' => '7','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '28','provinz_id' => '3','betrieb_id' => '8','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '29','provinz_id' => '3','betrieb_id' => '9','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '30','provinz_id' => '3','betrieb_id' => '10','max_menge' => '8','aktuelle_menge' => '8'),
			  array('id' => '31','provinz_id' => '4','betrieb_id' => '1','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '32','provinz_id' => '4','betrieb_id' => '2','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '33','provinz_id' => '4','betrieb_id' => '3','max_menge' => '7','aktuelle_menge' => '2'),
			  array('id' => '34','provinz_id' => '4','betrieb_id' => '4','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '35','provinz_id' => '4','betrieb_id' => '5','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '36','provinz_id' => '4','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '37','provinz_id' => '4','betrieb_id' => '7','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '38','provinz_id' => '4','betrieb_id' => '8','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '39','provinz_id' => '4','betrieb_id' => '9','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '40','provinz_id' => '4','betrieb_id' => '10','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '41','provinz_id' => '5','betrieb_id' => '1','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '42','provinz_id' => '5','betrieb_id' => '2','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '43','provinz_id' => '5','betrieb_id' => '3','max_menge' => '6','aktuelle_menge' => '6'),
			  array('id' => '44','provinz_id' => '5','betrieb_id' => '4','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '45','provinz_id' => '5','betrieb_id' => '5','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '46','provinz_id' => '5','betrieb_id' => '6','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '47','provinz_id' => '5','betrieb_id' => '7','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '48','provinz_id' => '5','betrieb_id' => '8','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '49','provinz_id' => '5','betrieb_id' => '9','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '50','provinz_id' => '5','betrieb_id' => '10','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '51','provinz_id' => '6','betrieb_id' => '1','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '52','provinz_id' => '6','betrieb_id' => '2','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '53','provinz_id' => '6','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '54','provinz_id' => '6','betrieb_id' => '4','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '55','provinz_id' => '6','betrieb_id' => '5','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '56','provinz_id' => '6','betrieb_id' => '6','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '57','provinz_id' => '6','betrieb_id' => '7','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '58','provinz_id' => '6','betrieb_id' => '8','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '59','provinz_id' => '6','betrieb_id' => '9','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '60','provinz_id' => '6','betrieb_id' => '10','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '61','provinz_id' => '7','betrieb_id' => '1','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '62','provinz_id' => '7','betrieb_id' => '2','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '63','provinz_id' => '7','betrieb_id' => '3','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '64','provinz_id' => '7','betrieb_id' => '4','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '65','provinz_id' => '7','betrieb_id' => '5','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '66','provinz_id' => '7','betrieb_id' => '6','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '67','provinz_id' => '7','betrieb_id' => '7','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '68','provinz_id' => '7','betrieb_id' => '8','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '69','provinz_id' => '7','betrieb_id' => '9','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '70','provinz_id' => '7','betrieb_id' => '10','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '71','provinz_id' => '8','betrieb_id' => '1','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '72','provinz_id' => '8','betrieb_id' => '2','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '73','provinz_id' => '8','betrieb_id' => '3','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '74','provinz_id' => '8','betrieb_id' => '4','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '75','provinz_id' => '8','betrieb_id' => '5','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '76','provinz_id' => '8','betrieb_id' => '6','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '77','provinz_id' => '8','betrieb_id' => '7','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '78','provinz_id' => '8','betrieb_id' => '8','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '79','provinz_id' => '8','betrieb_id' => '9','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '80','provinz_id' => '8','betrieb_id' => '10','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '81','provinz_id' => '9','betrieb_id' => '1','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '82','provinz_id' => '9','betrieb_id' => '2','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '83','provinz_id' => '9','betrieb_id' => '3','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '84','provinz_id' => '9','betrieb_id' => '4','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '85','provinz_id' => '9','betrieb_id' => '5','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '86','provinz_id' => '9','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '87','provinz_id' => '9','betrieb_id' => '7','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '88','provinz_id' => '9','betrieb_id' => '8','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '89','provinz_id' => '9','betrieb_id' => '9','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '90','provinz_id' => '9','betrieb_id' => '10','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '91','provinz_id' => '10','betrieb_id' => '1','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '92','provinz_id' => '10','betrieb_id' => '2','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '93','provinz_id' => '10','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '94','provinz_id' => '10','betrieb_id' => '4','max_menge' => '8','aktuelle_menge' => '8'),
			  array('id' => '95','provinz_id' => '10','betrieb_id' => '5','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '96','provinz_id' => '10','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '97','provinz_id' => '10','betrieb_id' => '7','max_menge' => '7','aktuelle_menge' => '7'),
			  array('id' => '98','provinz_id' => '10','betrieb_id' => '8','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '99','provinz_id' => '10','betrieb_id' => '9','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '100','provinz_id' => '10','betrieb_id' => '10','max_menge' => '6','aktuelle_menge' => '6'),
			  array('id' => '101','provinz_id' => '11','betrieb_id' => '1','max_menge' => '4','aktuelle_menge' => '3'),
			  array('id' => '102','provinz_id' => '11','betrieb_id' => '2','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '103','provinz_id' => '11','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '104','provinz_id' => '11','betrieb_id' => '4','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '105','provinz_id' => '11','betrieb_id' => '5','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '106','provinz_id' => '11','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '107','provinz_id' => '11','betrieb_id' => '7','max_menge' => '9','aktuelle_menge' => '7'),
			  array('id' => '108','provinz_id' => '11','betrieb_id' => '8','max_menge' => '6','aktuelle_menge' => '6'),
			  array('id' => '109','provinz_id' => '11','betrieb_id' => '9','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '110','provinz_id' => '11','betrieb_id' => '10','max_menge' => '10','aktuelle_menge' => '10'),
			  array('id' => '111','provinz_id' => '12','betrieb_id' => '1','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '112','provinz_id' => '12','betrieb_id' => '2','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '113','provinz_id' => '12','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '114','provinz_id' => '12','betrieb_id' => '4','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '115','provinz_id' => '12','betrieb_id' => '5','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '116','provinz_id' => '12','betrieb_id' => '6','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '117','provinz_id' => '12','betrieb_id' => '7','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '118','provinz_id' => '12','betrieb_id' => '8','max_menge' => '7','aktuelle_menge' => '7'),
			  array('id' => '119','provinz_id' => '12','betrieb_id' => '9','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '120','provinz_id' => '12','betrieb_id' => '10','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '121','provinz_id' => '13','betrieb_id' => '1','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '122','provinz_id' => '13','betrieb_id' => '2','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '123','provinz_id' => '13','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '124','provinz_id' => '13','betrieb_id' => '4','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '125','provinz_id' => '13','betrieb_id' => '5','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '126','provinz_id' => '13','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '127','provinz_id' => '13','betrieb_id' => '7','max_menge' => '6','aktuelle_menge' => '6'),
			  array('id' => '128','provinz_id' => '13','betrieb_id' => '8','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '129','provinz_id' => '13','betrieb_id' => '9','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '130','provinz_id' => '13','betrieb_id' => '10','max_menge' => '5','aktuelle_menge' => '5'),
			  array('id' => '131','provinz_id' => '14','betrieb_id' => '1','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '132','provinz_id' => '14','betrieb_id' => '2','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '133','provinz_id' => '14','betrieb_id' => '3','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '134','provinz_id' => '14','betrieb_id' => '4','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '135','provinz_id' => '14','betrieb_id' => '5','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '136','provinz_id' => '14','betrieb_id' => '6','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '137','provinz_id' => '14','betrieb_id' => '7','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '138','provinz_id' => '14','betrieb_id' => '8','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '139','provinz_id' => '14','betrieb_id' => '9','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '140','provinz_id' => '14','betrieb_id' => '10','max_menge' => '6','aktuelle_menge' => '6'),
			  array('id' => '141','provinz_id' => '15','betrieb_id' => '1','max_menge' => '7','aktuelle_menge' => '7'),
			  array('id' => '142','provinz_id' => '15','betrieb_id' => '2','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '143','provinz_id' => '15','betrieb_id' => '3','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '144','provinz_id' => '15','betrieb_id' => '4','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '145','provinz_id' => '15','betrieb_id' => '5','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '146','provinz_id' => '15','betrieb_id' => '6','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '147','provinz_id' => '15','betrieb_id' => '7','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '148','provinz_id' => '15','betrieb_id' => '8','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '149','provinz_id' => '15','betrieb_id' => '9','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '150','provinz_id' => '15','betrieb_id' => '10','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '151','provinz_id' => '16','betrieb_id' => '1','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '152','provinz_id' => '16','betrieb_id' => '2','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '153','provinz_id' => '16','betrieb_id' => '3','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '154','provinz_id' => '16','betrieb_id' => '4','max_menge' => '0','aktuelle_menge' => '0'),
			  array('id' => '155','provinz_id' => '16','betrieb_id' => '5','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '156','provinz_id' => '16','betrieb_id' => '6','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '157','provinz_id' => '16','betrieb_id' => '7','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '158','provinz_id' => '16','betrieb_id' => '8','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '159','provinz_id' => '16','betrieb_id' => '9','max_menge' => '2','aktuelle_menge' => '2'),
			  array('id' => '160','provinz_id' => '16','betrieb_id' => '10','max_menge' => '4','aktuelle_menge' => '4'),
			  array('id' => '161','provinz_id' => '17','betrieb_id' => '1','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '162','provinz_id' => '17','betrieb_id' => '2','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '163','provinz_id' => '17','betrieb_id' => '3','max_menge' => '1','aktuelle_menge' => '0'),
			  array('id' => '164','provinz_id' => '17','betrieb_id' => '4','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '165','provinz_id' => '17','betrieb_id' => '5','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '166','provinz_id' => '17','betrieb_id' => '6','max_menge' => '1','aktuelle_menge' => '1'),
			  array('id' => '167','provinz_id' => '17','betrieb_id' => '7','max_menge' => '10','aktuelle_menge' => '10'),
			  array('id' => '168','provinz_id' => '17','betrieb_id' => '8','max_menge' => '3','aktuelle_menge' => '1'),
			  array('id' => '169','provinz_id' => '17','betrieb_id' => '9','max_menge' => '3','aktuelle_menge' => '3'),
			  array('id' => '170','provinz_id' => '17','betrieb_id' => '10','max_menge' => '8','aktuelle_menge' => '8')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_provinz_rohstoff', $rsp_provinz_rohstoff);
	}

	public function insert_rsp_raenge()
	{
		$rsp_raenge = array(
			  array('id' => '1','land' => '2','stufe' => '1','beruf' => 'MIL','name' => 'Offizierskadett','url' => 'images/rsp/FRT/MIL/1.png'),
			  array('id' => '2','land' => '2','stufe' => '2','beruf' => 'MIL','name' => 'Leutnant','url' => 'images/rsp/FRT/MIL/2.png'),
			  array('id' => '3','land' => '2','stufe' => '3','beruf' => 'MIL','name' => 'Oberleutnant','url' => 'images/rsp/FRT/MIL/3.png'),
			  array('id' => '4','land' => '2','stufe' => '4','beruf' => 'MIL','name' => 'Hauptmann','url' => 'images/rsp/FRT/MIL/4.png'),
			  array('id' => '5','land' => '2','stufe' => '5','beruf' => 'MIL','name' => 'Major','url' => 'images/rsp/FRT/MIL/5.png'),
			  array('id' => '6','land' => '2','stufe' => '6','beruf' => 'MIL','name' => 'Oberstleutnant','url' => 'images/rsp/FRT/MIL/6.png'),
			  array('id' => '7','land' => '2','stufe' => '7','beruf' => 'MIL','name' => 'Oberst','url' => 'images/rsp/FRT/MIL/7.png'),
			  array('id' => '8','land' => '2','stufe' => '8','beruf' => 'MIL','name' => 'Generalmajor','url' => 'images/rsp/FRT/MIL/8.png'),
			  array('id' => '9','land' => '2','stufe' => '9','beruf' => 'MIL','name' => 'Generalleutnant','url' => 'images/rsp/FRT/MIL/9.png'),
			  array('id' => '10','land' => '2','stufe' => '10','beruf' => 'MIL','name' => 'Generaloberst','url' => 'images/rsp/FRT/MIL/10.png'),
			  array('id' => '11','land' => '2','stufe' => '11','beruf' => 'MIL','name' => 'General','url' => 'images/rsp/FRT/MIL/11.png'),
			  array('id' => '12','land' => '2','stufe' => '12','beruf' => 'MIL','name' => 'Marschall von Tadsowien','url' => 'images/rsp/FRT/MIL/12.png'),
			  array('id' => '13','land' => '4','stufe' => '1','beruf' => 'MIL','name' => 'Offizierskadett','url' => 'images/rsp/VRB/MIL/1.png'),
			  array('id' => '14','land' => '4','stufe' => '2','beruf' => 'MIL','name' => 'Leutnant','url' => 'images/rsp/VRB/MIL/2.png'),
			  array('id' => '15','land' => '4','stufe' => '3','beruf' => 'MIL','name' => 'Oberleutnant','url' => 'images/rsp/VRB/MIL/3.png'),
			  array('id' => '16','land' => '4','stufe' => '4','beruf' => 'MIL','name' => 'Hauptmann','url' => 'images/rsp/VRB/MIL/4.png'),
			  array('id' => '17','land' => '4','stufe' => '5','beruf' => 'MIL','name' => 'Major','url' => 'images/rsp/VRB/MIL/5.png'),
			  array('id' => '18','land' => '4','stufe' => '6','beruf' => 'MIL','name' => 'Oberstleutnant','url' => 'images/rsp/VRB/MIL/6.png'),
			  array('id' => '19','land' => '4','stufe' => '7','beruf' => 'MIL','name' => 'Oberst','url' => 'images/rsp/VRB/MIL/7.png'),
			  array('id' => '20','land' => '4','stufe' => '8','beruf' => 'MIL','name' => 'Generalmajor','url' => 'images/rsp/VRB/MIL/8.png'),
			  array('id' => '21','land' => '4','stufe' => '9','beruf' => 'MIL','name' => 'Generalleutnant','url' => 'images/rsp/VRB/MIL/9.png'),
			  array('id' => '22','land' => '4','stufe' => '10','beruf' => 'MIL','name' => 'Generaloberst','url' => 'images/rsp/VRB/MIL/10.png'),
			  array('id' => '23','land' => '4','stufe' => '11','beruf' => 'MIL','name' => 'Armeegeneral','url' => 'images/rsp/VRB/MIL/11.png'),
			  array('id' => '24','land' => '4','stufe' => '12','beruf' => 'MIL','name' => 'Marschall von Bakirien','url' => 'images/rsp/VRB/MIL/12.png'),
			  array('id' => '25','land' => '3','stufe' => '1','beruf' => 'MIL','name' => 'Offizierskadett','url' => 'images/rsp/USR/MIL/1.png'),
			  array('id' => '26','land' => '3','stufe' => '2','beruf' => 'MIL','name' => 'Leutnant','url' => 'images/rsp/USR/MIL/2.png'),
			  array('id' => '27','land' => '3','stufe' => '3','beruf' => 'MIL','name' => 'Oberleutnant','url' => 'images/rsp/USR/MIL/3.png'),
			  array('id' => '28','land' => '3','stufe' => '4','beruf' => 'MIL','name' => 'Hauptmann','url' => 'images/rsp/USR/MIL/4.png'),
			  array('id' => '29','land' => '3','stufe' => '5','beruf' => 'MIL','name' => 'Major','url' => 'images/rsp/USR/MIL/5.png'),
			  array('id' => '30','land' => '3','stufe' => '6','beruf' => 'MIL','name' => 'Oberstleutnant','url' => 'images/rsp/USR/MIL/6.png'),
			  array('id' => '31','land' => '3','stufe' => '7','beruf' => 'MIL','name' => 'Oberst','url' => 'images/rsp/USR/MIL/7.png'),
			  array('id' => '32','land' => '3','stufe' => '8','beruf' => 'MIL','name' => 'Generalmajor','url' => 'images/rsp/USR/MIL/8.png'),
			  array('id' => '33','land' => '3','stufe' => '9','beruf' => 'MIL','name' => 'Generalleutnant','url' => 'images/rsp/USR/MIL/9.png'),
			  array('id' => '34','land' => '3','stufe' => '10','beruf' => 'MIL','name' => 'Generaloberst','url' => 'images/rsp/USR/MIL/10.png'),
			  array('id' => '35','land' => '3','stufe' => '11','beruf' => 'MIL','name' => 'General','url' => 'images/rsp/USR/MIL/11.png'),
			  array('id' => '36','land' => '3','stufe' => '12','beruf' => 'MIL','name' => 'Feldmarschall','url' => 'images/rsp/USR/MIL/12.png'),
			  array('id' => '37','land' => '2','stufe' => '1','beruf' => 'POL','name' => 'Offiziersanwärter','url' => 'images/rsp/FRT/POL/1.png'),
			  array('id' => '38','land' => '2','stufe' => '2','beruf' => 'POL','name' => 'Leutnant','url' => 'images/rsp/FRT/POL/2.png'),
			  array('id' => '39','land' => '2','stufe' => '3','beruf' => 'POL','name' => 'Oberleutnant','url' => 'images/rsp/FRT/POL/3.png'),
			  array('id' => '40','land' => '2','stufe' => '4','beruf' => 'POL','name' => 'Hauptmann','url' => 'images/rsp/FRT/POL/4.png'),
			  array('id' => '41','land' => '2','stufe' => '5','beruf' => 'POL','name' => 'Major','url' => 'images/rsp/FRT/POL/5.png'),
			  array('id' => '42','land' => '2','stufe' => '6','beruf' => 'POL','name' => 'Oberstleutnant','url' => 'images/rsp/FRT/POL/6.png'),
			  array('id' => '43','land' => '2','stufe' => '7','beruf' => 'POL','name' => 'Oberst','url' => 'images/rsp/FRT/POL/7.png'),
			  array('id' => '44','land' => '2','stufe' => '8','beruf' => 'POL','name' => 'Generalmajor','url' => 'images/rsp/FRT/POL/8.png'),
			  array('id' => '45','land' => '2','stufe' => '9','beruf' => 'POL','name' => 'Generalleutnant','url' => 'images/rsp/FRT/POL/9.png'),
			  array('id' => '46','land' => '2','stufe' => '10','beruf' => 'POL','name' => 'Generaloberst','url' => 'images/rsp/FRT/POL/10.png'),
			  array('id' => '47','land' => '2','stufe' => '11','beruf' => 'POL','name' => 'General','url' => 'images/rsp/FRT/POL/11.png'),
			  array('id' => '48','land' => '2','stufe' => '12','beruf' => 'POL','name' => 'Marschall von Tadsowien','url' => 'images/rsp/FRT/POL/12.png'),
			  array('id' => '49','land' => '4','stufe' => '1','beruf' => 'POL','name' => 'Offiziersanwärter','url' => 'images/rsp/VRB/POL/1.png'),
			  array('id' => '50','land' => '4','stufe' => '2','beruf' => 'POL','name' => 'Leutnant','url' => 'images/rsp/VRB/POL/2.png'),
			  array('id' => '51','land' => '4','stufe' => '3','beruf' => 'POL','name' => 'Oberleutnant','url' => 'images/rsp/VRB/POL/3.png'),
			  array('id' => '52','land' => '4','stufe' => '4','beruf' => 'POL','name' => 'Hauptmann','url' => 'images/rsp/VRB/POL/4.png'),
			  array('id' => '53','land' => '4','stufe' => '5','beruf' => 'POL','name' => 'Major','url' => 'images/rsp/VRB/POL/5.png'),
			  array('id' => '54','land' => '4','stufe' => '6','beruf' => 'POL','name' => 'Oberstleutnant','url' => 'images/rsp/VRB/POL/6.png'),
			  array('id' => '55','land' => '4','stufe' => '7','beruf' => 'POL','name' => 'Oberst','url' => 'images/rsp/VRB/POL/7.png'),
			  array('id' => '56','land' => '4','stufe' => '8','beruf' => 'POL','name' => 'Generalmajor','url' => 'images/rsp/VRB/POL/8.png'),
			  array('id' => '57','land' => '4','stufe' => '9','beruf' => 'POL','name' => 'Generalleutnant','url' => 'images/rsp/VRB/POL/9.png'),
			  array('id' => '58','land' => '4','stufe' => '10','beruf' => 'POL','name' => 'Generaloberst','url' => 'images/rsp/VRB/POL/10.png'),
			  array('id' => '59','land' => '4','stufe' => '11','beruf' => 'POL','name' => 'Armeegeneral','url' => 'images/rsp/VRB/POL/11.png'),
			  array('id' => '60','land' => '4','stufe' => '12','beruf' => 'POL','name' => 'Marschall von Bakirien','url' => 'images/rsp/VRB/POL/12.png'),
			  array('id' => '61','land' => '3','stufe' => '1','beruf' => 'POL','name' => 'Offizierskadett','url' => 'images/rsp/USR/POL/1.png'),
			  array('id' => '62','land' => '3','stufe' => '2','beruf' => 'POL','name' => 'Leutnant','url' => 'images/rsp/USR/POL/2.png'),
			  array('id' => '63','land' => '3','stufe' => '3','beruf' => 'POL','name' => 'Oberleutnant','url' => 'images/rsp/USR/POL/3.png'),
			  array('id' => '64','land' => '3','stufe' => '4','beruf' => 'POL','name' => 'Hauptmann','url' => 'images/rsp/USR/POL/4.png'),
			  array('id' => '65','land' => '3','stufe' => '5','beruf' => 'POL','name' => 'Major','url' => 'images/rsp/USR/POL/5.png'),
			  array('id' => '66','land' => '3','stufe' => '6','beruf' => 'POL','name' => 'Oberstleutnant','url' => 'images/rsp/USR/POL/6.png'),
			  array('id' => '67','land' => '3','stufe' => '7','beruf' => 'POL','name' => 'Oberst','url' => 'images/rsp/USR/POL/7.png'),
			  array('id' => '68','land' => '3','stufe' => '8','beruf' => 'POL','name' => 'Generalmajor','url' => 'images/rsp/USR/POL/8.png'),
			  array('id' => '69','land' => '3','stufe' => '9','beruf' => 'POL','name' => 'Generalleutnant','url' => 'images/rsp/USR/POL/9.png'),
			  array('id' => '70','land' => '3','stufe' => '10','beruf' => 'POL','name' => 'Generaloberst','url' => 'images/rsp/USR/POL/10.png'),
			  array('id' => '71','land' => '3','stufe' => '11','beruf' => 'POL','name' => 'General','url' => 'images/rsp/USR/POL/11.png'),
			  array('id' => '72','land' => '3','stufe' => '12','beruf' => 'POL','name' => 'Feldmarschall','url' => 'images/rsp/USR/POL/12.png'),
			  array('id' => '73','land' => '2','stufe' => '1','beruf' => 'REG','name' => 'Amtsanwärter','url' => 'images/rsp/FRT/REG/1.png'),
			  array('id' => '74','land' => '2','stufe' => '2','beruf' => 'REG','name' => 'Amtsassistent','url' => 'images/rsp/FRT/REG/2.png'),
			  array('id' => '75','land' => '2','stufe' => '3','beruf' => 'REG','name' => 'Oberamtsassistent','url' => 'images/rsp/FRT/REG/3.png'),
			  array('id' => '76','land' => '2','stufe' => '4','beruf' => 'REG','name' => 'Amtssekretär','url' => 'images/rsp/FRT/REG/4.png'),
			  array('id' => '77','land' => '2','stufe' => '5','beruf' => 'REG','name' => 'Oberamtssekretär','url' => 'images/rsp/FRT/REG/5.png'),
			  array('id' => '78','land' => '2','stufe' => '6','beruf' => 'REG','name' => 'Amtsinspektor','url' => 'images/rsp/FRT/REG/6.png'),
			  array('id' => '79','land' => '2','stufe' => '7','beruf' => 'REG','name' => 'Oberamtsinspektor','url' => 'images/rsp/FRT/REG/7.png'),
			  array('id' => '80','land' => '2','stufe' => '8','beruf' => 'REG','name' => 'Regierungsrat','url' => 'images/rsp/FRT/REG/8.png'),
			  array('id' => '81','land' => '2','stufe' => '9','beruf' => 'REG','name' => 'Oberregierungsrat','url' => 'images/rsp/FRT/REG/9.png'),
			  array('id' => '82','land' => '2','stufe' => '10','beruf' => 'REG','name' => 'Ministerialrat','url' => 'images/rsp/FRT/REG/10.png'),
			  array('id' => '83','land' => '2','stufe' => '11','beruf' => 'REG','name' => 'Oberministerialrat','url' => 'images/rsp/FRT/REG/11.png'),
			  array('id' => '84','land' => '2','stufe' => '12','beruf' => 'REG','name' => 'Ministerialdirektor','url' => 'images/rsp/FRT/REG/12.png'),
			  array('id' => '85','land' => '4','stufe' => '1','beruf' => 'REG','name' => 'Amtsanwärter','url' => 'images/rsp/VRB/REG/1.png'),
			  array('id' => '86','land' => '4','stufe' => '2','beruf' => 'REG','name' => 'Amtssekretär','url' => 'images/rsp/VRB/REG/2.png'),
			  array('id' => '87','land' => '4','stufe' => '3','beruf' => 'REG','name' => 'Oberamtssekretär','url' => 'images/rsp/VRB/REG/3.png'),
			  array('id' => '88','land' => '4','stufe' => '4','beruf' => 'REG','name' => 'Amtsmeister','url' => 'images/rsp/VRB/REG/4.png'),
			  array('id' => '89','land' => '4','stufe' => '5','beruf' => 'REG','name' => 'Oberamtsmeister','url' => 'images/rsp/VRB/REG/5.png'),
			  array('id' => '90','land' => '4','stufe' => '6','beruf' => 'REG','name' => 'Amtsinspektor','url' => 'images/rsp/VRB/REG/6.png'),
			  array('id' => '91','land' => '4','stufe' => '7','beruf' => 'REG','name' => 'Oberamtsinspektor','url' => 'images/rsp/VRB/REG/7.png'),
			  array('id' => '92','land' => '4','stufe' => '8','beruf' => 'REG','name' => 'Regierungsrat','url' => 'images/rsp/VRB/REG/8.png'),
			  array('id' => '93','land' => '4','stufe' => '9','beruf' => 'REG','name' => 'Oberregierungsrat','url' => 'images/rsp/VRB/REG/9.png'),
			  array('id' => '94','land' => '4','stufe' => '10','beruf' => 'REG','name' => 'Ministerialrat','url' => 'images/rsp/VRB/REG/10.png'),
			  array('id' => '95','land' => '4','stufe' => '11','beruf' => 'REG','name' => 'Oberministerialrat','url' => 'images/rsp/VRB/REG/11.png'),
			  array('id' => '96','land' => '4','stufe' => '12','beruf' => 'REG','name' => 'Ministerialdirektor','url' => 'images/rsp/VRB/REG/12.png'),
			  array('id' => '97','land' => '3','stufe' => '1','beruf' => 'REG','name' => 'Amtsanwärter','url' => 'images/rsp/USR/REG/1.png'),
			  array('id' => '98','land' => '3','stufe' => '2','beruf' => 'REG','name' => 'Amtssekretär','url' => 'images/rsp/USR/REG/2.png'),
			  array('id' => '99','land' => '3','stufe' => '3','beruf' => 'REG','name' => 'Oberamtssekretär','url' => 'images/rsp/USR/REG/3.png'),
			  array('id' => '100','land' => '3','stufe' => '4','beruf' => 'REG','name' => 'Amtsmeister','url' => 'images/rsp/USR/REG/4.png'),
			  array('id' => '101','land' => '3','stufe' => '5','beruf' => 'REG','name' => 'Oberamtsmeister','url' => 'images/rsp/USR/REG/5.png'),
			  array('id' => '102','land' => '3','stufe' => '6','beruf' => 'REG','name' => 'Amtsinspektor','url' => 'images/rsp/USR/REG/6.png'),
			  array('id' => '103','land' => '3','stufe' => '7','beruf' => 'REG','name' => 'Oberamtsinspektor','url' => 'images/rsp/USR/REG/7.png'),
			  array('id' => '104','land' => '3','stufe' => '8','beruf' => 'REG','name' => 'Regierungsrat','url' => 'images/rsp/USR/REG/8.png'),
			  array('id' => '105','land' => '3','stufe' => '9','beruf' => 'REG','name' => 'Oberregierungsrat','url' => 'images/rsp/USR/REG/9.png'),
			  array('id' => '106','land' => '3','stufe' => '10','beruf' => 'REG','name' => 'Ministerialrat','url' => 'images/rsp/USR/REG/10.png'),
			  array('id' => '107','land' => '3','stufe' => '11','beruf' => 'REG','name' => 'Oberministerialrat','url' => 'images/rsp/USR/REG/11.png'),
			  array('id' => '108','land' => '3','stufe' => '12','beruf' => 'REG','name' => 'Ministerialdirektor','url' => 'images/rsp/USR/REG/12.png'),
			  array('id' => '109','land' => '1','stufe' => '1','beruf' => 'BEO','name' => 'Beobachter','url' => 'images/rsp/neutral/Beobachter.png'),
			  array('id' => '110','land' => '1','stufe' => '1','beruf' => 'MIT','name' => 'Mitarbeiter','url' => 'images/rsp/neutral/Spielleiter.png'),
			  array('id' => '111','land' => '1','stufe' => '1','beruf' => 'ZIV','name' => 'Zivilbürger','url' => 'images/rsp/neutral/zivilbuerger.png'),
			  array('id' => '114','land' => '0','stufe' => '0','beruf' => 'GOV','name' => 'Provinzgouverneur','url' => 'images/rsp/icon_amt/provinzgouverneur.png'),
			  array('id' => '115','land' => '0','stufe' => '0','beruf' => 'PRA','name' => 'Staatspräsident','url' => 'images/rsp/icon_amt/staatspraesident.png'),
			  array('id' => '116','land' => '0','stufe' => '0','beruf' => 'MIN','name' => 'Minister','url' => 'images/rsp/icon_amt/minister.png'),
			  array('id' => '117','land' => '0','stufe' => '0','beruf' => 'SEK','name' => 'Staatssekretär','url' => 'images/rsp/icon_amt/staatssekretaer.png'),
			  array('id' => '118','land' => '1','stufe' => '1','beruf' => 'USA','name' => 'Major','url' => 'images/rsp/uncf/usa/us_army_1.png'),
			  array('id' => '119','land' => '1','stufe' => '2','beruf' => 'USA','name' => 'Oberstleutnant','url' => 'images/rsp/uncf/usa/us_army_2.png'),
			  array('id' => '120','land' => '1','stufe' => '3','beruf' => 'USA','name' => 'Oberst','url' => 'images/rsp/uncf/usa/us_army_3.png'),
			  array('id' => '121','land' => '1','stufe' => '4','beruf' => 'USA','name' => 'Brigadegeneral','url' => 'images/rsp/uncf/usa/us_army_4.png'),
			  array('id' => '122','land' => '1','stufe' => '5','beruf' => 'USA','name' => 'Generalmajor','url' => 'images/rsp/uncf/usa/us_army_5.png'),
			  array('id' => '123','land' => '1','stufe' => '6','beruf' => 'USA','name' => 'Generalleutnant','url' => 'images/rsp/uncf/usa/us_army_6.png'),
			  array('id' => '124','land' => '1','stufe' => '7','beruf' => 'USA','name' => 'General','url' => 'images/rsp/uncf/usa/us_army_7.png'),
			  array('id' => '125','land' => '1','stufe' => '8','beruf' => 'USA','name' => 'General der Armee','url' => 'images/rsp/uncf/usa/us_army_8.png'),
			  array('id' => '126','land' => '1','stufe' => '9','beruf' => 'USA','name' => 'Vizeadmiral','url' => 'images/rsp/uncf/usa/us_army_9.png'),
			  array('id' => '127','land' => '1','stufe' => '10','beruf' => 'USA','name' => 'Admiral','url' => 'images/rsp/uncf/usa/us_army_10.png'),
			  array('id' => '128','land' => '1','stufe' => '11','beruf' => 'USA','name' => 'Flottenadmiral','url' => 'images/rsp/uncf/usa/us_army_11.png'),
			  array('id' => '129','land' => '1','stufe' => '1','beruf' => 'IND','name' => 'Major','url' => 'images/rsp/uncf/india/indian_army_1.png'),
			  array('id' => '130','land' => '1','stufe' => '2','beruf' => 'IND','name' => 'Oberstleutnant','url' => 'images/rsp/uncf/india/indian_army_2.png'),
			  array('id' => '131','land' => '1','stufe' => '3','beruf' => 'IND','name' => 'Oberst','url' => 'images/rsp/uncf/india/indian_army_3.png'),
			  array('id' => '132','land' => '1','stufe' => '4','beruf' => 'IND','name' => 'Brigadier','url' => 'images/rsp/uncf/india/indian_army_4.png'),
			  array('id' => '133','land' => '1','stufe' => '5','beruf' => 'IND','name' => 'Generalmajor','url' => 'images/rsp/uncf/india/indian_army_5.png'),
			  array('id' => '134','land' => '1','stufe' => '6','beruf' => 'IND','name' => 'Generalleutnant','url' => 'images/rsp/uncf/india/indian_army_6.png'),
			  array('id' => '135','land' => '1','stufe' => '7','beruf' => 'IND','name' => 'General','url' => 'images/rsp/uncf/india/indian_army_7.png'),
			  array('id' => '136','land' => '1','stufe' => '8','beruf' => 'IND','name' => 'Feldmarschall','url' => 'images/rsp/uncf/india/indian_army_8.png'),
			  array('id' => '137','land' => '1','stufe' => '1','beruf' => 'EGY','name' => 'Major','url' => 'images/rsp/uncf/egyptian/egyptian_army_1.png'),
			  array('id' => '138','land' => '1','stufe' => '2','beruf' => 'EGY','name' => 'Oberstleutnant','url' => 'images/rsp/uncf/egyptian/egyptian_army_2.png'),
			  array('id' => '139','land' => '1','stufe' => '3','beruf' => 'EGY','name' => 'Oberst','url' => 'images/rsp/uncf/egyptian/egyptian_army_3.png'),
			  array('id' => '140','land' => '1','stufe' => '4','beruf' => 'EGY','name' => 'Brigadegeneral','url' => 'images/rsp/uncf/egyptian/egyptian_army_4.png'),
			  array('id' => '141','land' => '1','stufe' => '5','beruf' => 'EGY','name' => 'Generalmajor','url' => 'images/rsp/uncf/egyptian/egyptian_army_5.png'),
			  array('id' => '142','land' => '1','stufe' => '6','beruf' => 'EGY','name' => 'Generalleutnant','url' => 'images/rsp/uncf/egyptian/egyptian_army_6.png'),
			  array('id' => '143','land' => '1','stufe' => '7','beruf' => 'EGY','name' => 'Generaloberst','url' => 'images/rsp/uncf/egyptian/egyptian_army_7.png'),
			  array('id' => '144','land' => '1','stufe' => '8','beruf' => 'EGY','name' => 'Feldmarschall','url' => 'images/rsp/uncf/egyptian/egyptian_army_8.png'),
			  array('id' => '145','land' => '1','stufe' => '1','beruf' => 'AD','name' => 'Außer Dienst','url' => 'images/rsp/neutral/ausser_dienst.png')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_raenge', $rsp_raenge);
	}

	public function insert_rsp_ressourcen()
	{
		$rsp_ressourcen = array(
			  array('id' => '1','name' => 'Credits','url' => 'images/rsp/icon_ress/credits.png','bereich_id' => '1'),
			  array('id' => '2','name' => 'Erz','url' => 'images/rsp/icon_ress/erz.png','bereich_id' => '1'),
			  array('id' => '3','name' => 'Edelmetall','url' => 'images/rsp/icon_ress/edelmetall.png','bereich_id' => '1'),
			  array('id' => '4','name' => 'Rohöl','url' => 'images/rsp/icon_ress/rohoel.png','bereich_id' => '1'),
			  array('id' => '5','name' => 'Kohle','url' => 'images/rsp/icon_ress/kohle.png','bereich_id' => '1'),
			  array('id' => '6','name' => 'Erdgas','url' => 'images/rsp/icon_ress/erdgas.png','bereich_id' => '1'),
			  array('id' => '7','name' => 'Seltene Erden','url' => 'images/rsp/icon_ress/seltene_erden.png','bereich_id' => '1'),
			  array('id' => '8','name' => 'Trinkwasser','url' => 'images/rsp/icon_ress/trinkwasser.png','bereich_id' => '1'),
			  array('id' => '9','name' => 'Holz','url' => 'images/rsp/icon_ress/holz.png','bereich_id' => '1'),
			  array('id' => '10','name' => 'Zement','url' => 'images/rsp/icon_ress/zement.png','bereich_id' => '1'),
			  array('id' => '11','name' => 'Landwirtschaft','url' => 'images/rsp/icon_ress/landwirtschaft.png','bereich_id' => '1'),
			  array('id' => '12','name' => 'Textilprodukt','url' => 'images/rsp/icon_ress/textilprodukt.png','bereich_id' => '2'),
			  array('id' => '13','name' => 'Stahl','url' => 'images/rsp/icon_ress/stahl.png','bereich_id' => '2'),
			  array('id' => '14','name' => 'Seltenerd-Metall','url' => 'images/rsp/icon_ress/seltenerd_metall.png','bereich_id' => '2'),
			  array('id' => '15','name' => 'Diesel-Treibstoff','url' => 'images/rsp/icon_ress/diesel_treibstoff.png','bereich_id' => '2'),
			  array('id' => '16','name' => 'Chemischer Grundstoff','url' => 'images/rsp/icon_ress/chemische_grundstoffe.png','bereich_id' => '2'),
			  array('id' => '17','name' => 'Nahrungsmittel','url' => 'images/rsp/icon_ress/nahrungsmittel.png','bereich_id' => '2'),
			  array('id' => '18','name' => 'Militärische Versorgungsgüter','url' => 'images/rsp/icon_ress/militaerische_versorgungsgueter.png','bereich_id' => '2'),
			  array('id' => '19','name' => 'Billige Konsumgüter','url' => 'images/rsp/icon_ress/billige_konsumgüter.png','bereich_id' => '2'),
			  array('id' => '20','name' => 'Tabakwaren','url' => 'images/rsp/icon_ress/tabakwaren.png','bereich_id' => '2'),
			  array('id' => '21','name' => 'Handwaffen-Munition','url' => 'images/rsp/icon_ress/handwaffenmunition.png','bereich_id' => '2'),
			  array('id' => '22','name' => 'Einfache technische Bauteile','url' => 'images/rsp/icon_ress/einfache_technische_bauteile.png','bereich_id' => '2'),
			  array('id' => '23','name' => 'Fortschrittliche chemische Substanz','url' => 'images/rsp/icon_ress/fortschrittliche_chemische_substanz.png','bereich_id' => '3'),
			  array('id' => '24','name' => 'Infanteriewaffe','url' => 'images/rsp/icon_ress/infanteriewaffe.png','bereich_id' => '3'),
			  array('id' => '25','name' => 'Schwere Munition / Sprengmittel','url' => 'images/rsp/icon_ress/schwere_munition_sprengmittel.png','bereich_id' => '3'),
			  array('id' => '26','name' => 'Fortschrittliche technische Bauteile','url' => 'images/rsp/icon_ress/fortschrittliche_technische_bauteile.png','bereich_id' => '3'),
			  array('id' => '27','name' => 'Artillerie','url' => 'images/rsp/icon_ress/artillerie.png','bereich_id' => '3'),
			  array('id' => '28','name' => 'Medizinische Feldausrüstung','url' => 'images/rsp/icon_ress/medizinische_feldausruestung.png','bereich_id' => '3'),
			  array('id' => '29','name' => 'Optisches System','url' => 'images/rsp/icon_ress/optisches_system.png','bereich_id' => '3'),
			  array('id' => '30','name' => 'Transport- und Nutzfahrzeug','url' => 'images/rsp/icon_ress/transport_und_nutzfahrzeug.png','bereich_id' => '3'),
			  array('id' => '31','name' => 'Lokales Radarsystem','url' => 'images/rsp/icon_ress/lokales_radarsystem.png','bereich_id' => '3'),
			  array('id' => '32','name' => 'Gepanzertes Fahrzeug','url' => 'images/rsp/icon_ress/gepanzertes_fahrzeug.png','bereich_id' => '3'),
			  array('id' => '33','name' => 'Panzer (80er Jahre)','url' => 'images/rsp/icon_ress/panzer_80er.png','bereich_id' => '4'),
			  array('id' => '34','name' => 'reaktives Panzerungs-System','url' => 'images/rsp/icon_ress/reaktives_panzerungssystem.png','bereich_id' => '4'),
			  array('id' => '35','name' => 'Infanterie-Schutzausrüstung','url' => 'images/rsp/icon_ress/infanterie_schutzausruestung.png','bereich_id' => '4'),
			  array('id' => '36','name' => 'Mikroelektronisches Bauteil','url' => 'images/rsp/icon_ress/mikroelektronisches_bauteil.png','bereich_id' => '4'),
			  array('id' => '37','name' => 'Regionales Radarsystem','url' => 'images/rsp/icon_ress/regionales_radarsystem.png','bereich_id' => '4'),
			  array('id' => '38','name' => 'Chemiewaffe','url' => 'images/rsp/icon_ress/chemiewaffe.png','bereich_id' => '5'),
			  array('id' => '39','name' => 'Panzerabwehr-Lenkwaffe','url' => 'images/rsp/icon_ress/antipanzerlenkwaffe.png','bereich_id' => '5'),
			  array('id' => '40','name' => 'Kurzstrecken-Rakete','url' => 'images/rsp/icon_ress/kurzstreckenrakete.png','bereich_id' => '5'),
			  array('id' => '41','name' => 'Aufklärungsdrohne','url' => 'images/rsp/icon_ress/drohne.png','bereich_id' => '5'),
			  array('id' => '42','name' => 'Transportflugzeug','url' => 'images/rsp/icon_ress/transportflugzeug.png','bereich_id' => '5'),
			  array('id' => '43','name' => 'Verbessertes Kommunikationssystem','url' => 'images/rsp/icon_ress/verbessertes_kommunikationssystem.png','bereich_id' => '5')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_ressourcen', $rsp_ressourcen);
	}

	public function insert_rsp_ressourcen_bereich()
	{
		$rsp_ressourcen_bereich = array(
			  array('id' => '1','name' => 'Grund-Ressourcen'),
			  array('id' => '2','name' => 'Einfache Produktionsgüter'),
			  array('id' => '3','name' => 'Fortschrittliche Produktionsgüter'),
			  array('id' => '4','name' => 'Hochwertige Produktionsgüter'),
			  array('id' => '5','name' => 'Komplexe Produktionsgüter')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_ressourcen_bereich', $rsp_ressourcen_bereich);
	}

	public function insert_rsp_story()
	{
		$rsp_story = array(
			array('id' => '1','uberschrift' => 'ConSim Episode Test 01','text' => 'Ein neuer Tag bricht an. Das noch schwache Sonnenlicht durchbricht die Vorhänge Deines Schlafzimmers und spiegelt sich in verschwommenen Formen an den Wänden.
			Gestern Abend hast Du vergessen den Fernseher auszuschalten und im Morgenprogramm präsentiert man Dir gerade das Sitzfleisch 60jähriger Frauen beim rhythmischen Morgensport.
			Zwar erinnerst Du Dich nicht mehr allzu deutlich daran, aber es gab eine Aufgabe die Du heute noch erledigen musstest:')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_story', $rsp_story);
	}

	public function insert_rsp_story_actions()
	{
		$rsp_story_actions = array(
			  array('id' => '1','text' => 'Nichts','art' => '0'),
			  array('id' => '2','text' => 'Spielerkonto erhält Credits: ','art' => '1')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_story_actions', $rsp_story_actions);
	}

	public function insert_rsp_story_options()
	{
		$rsp_story_options = array(
			  array('id' => '1','part_id' => '1','uberschrift' => 'Stimmt! Ich sollte mich noch einmal genüsslich umdrehen und weiterschlafen! Zum Wohle des Vaterlandes!','action_id' => '1','wert' => '0','next_part' => '2'),
			  array('id' => '2','part_id' => '1','uberschrift' => 'Die Fernseh-Sendung im staatlichen Fernsehen hatte dazu aufgerufen, die Möglichkeiten wirtschaftlicher Betätigung für jeden Staatsbürger zu erkunden. Das habe ich natürlich als höriger und unkritischer Medienkonsument nicht vergessen!','action_id' => '1','wert' => '0','next_part' => '3'),
			  array('id' => '3','part_id' => '2','uberschrift' => 'Weiter!','action_id' => '1','wert' => '0','next_part' => '0'),
			  array('id' => '4','part_id' => '3','uberschrift' => 'Weiter!','action_id' => '2','wert' => '20','next_part' => '0')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_story_options', $rsp_story_options);
	}

	public function insert_rsp_story_part()
	{
		$rsp_story_part = array(
			array('id' => '1','story_id' => '1','part' => '1','uberschrift' => 'ConSim Episode Test 01 Part 1','text' => 'Ein neuer Tag bricht an. Das noch schwache Sonnenlicht durchbricht die Vorhänge Deines Schlafzimmers und spiegelt sich in verschwommenen Formen an den Wänden.
			Gestern Abend hast Du vergessen den Fernseher auszuschalten und im Morgenprogramm präsentiert man Dir gerade das Sitzfleisch 60jähriger Frauen beim rhythmischen Morgensport.
			Zwar erinnerst Du Dich nicht mehr allzu deutlich daran, aber es gab eine Aufgabe die Du heute noch erledigen musstest:'),
			array('id' => '2','story_id' => '1','part' => '2','uberschrift' => 'ConSim Episode Test 01 Part 2','text' => 'Nach kurzer Zeit döst Du wieder ein, nur um plötzlich erschreckt aus dem Schlaf hochzufahren. Natürlich bestand die Aufgabe nicht darin, weiter zu schlafen. Du fühlst Dich gezwungenermaßen daran erinnert, dass Du Dich mit den Eigenheiten wirtschaftlicher Bestätigung beschäftigen solltest!'),
			array('id' => '3','story_id' => '1','part' => '3','uberschrift' => 'ConSim Episode Test 01 Part 3','text' => 'Der Staat kann sich jederzeit auf Dich verlassen. Getreu erhebst Du Dich aus dem Bett. Dabei fällt Dir auf, dass unbekannte Argumentationshelfer als Motivationshilfe ein kleines Bündel Scheine unter Deiner Wohnungstür durch geschoben haben. Du erhältst als Ansporn für Deine staatsbürgerliche Einsatzbereitschaft 20 Credits!')
		);

		$this->db->sql_multi_insert($this->table_prefix . 'rsp_story_part', $rsp_story_part);
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
