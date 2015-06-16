<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\entity;

/**
* Entity for a single gebaude
*/
class unternehmen_betrieb extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'     				=> 'integer',
		'unternehmen_id'        => 'integer',
		'betrieb_id'		    => 'object',
		'provinz_id'            => 'object',
		'aktuelle_produktion'	=> 'integer',
		'anzahl_produktion'		=> 'integer',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array(
		'betrieb_id'			=> 'betrieb',
		'provinz_id'			=> 'provinz',
	);

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'unternehmen_id',
		'aktuelle_produktion',
		'anzahl_produktion',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $db_prefix	   The prefix of database table
	* @return \tacitus89\rsp_extension\entity\gebaude
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $db_prefix)
	{
		$this->db = $db;
		$this->db_prefix = $db_prefix;
	}

	/**
	* Generated the beginning SQL-Select Part
	* WHERE and Order missing
	*
	* @param string  $db_prefix	   The prefix of database table
	* @return string The beginning sql select
	* @access public
	*/
	public static function get_sql_select($db_prefix)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('unternehmen_betrieb' => 'ub', 'betrieb' => 'b', 'gebaude' => 'g', 'provinz' => 'p')) .'
				FROM '. $db_prefix.\tacitus89\rsp\tables::$table['unternehmen_betriebe'] .' ub
				LEFT JOIN '. $db_prefix.\tacitus89\rsp\tables::$table['betriebe'] .' b ON ub.betrieb_id = b.id
				LEFT JOIN '. $db_prefix.\tacitus89\rsp\tables::$table['gebaude'] .' g ON b.gebaude_id = g.id
				LEFT JOIN '. $db_prefix.\tacitus89\rsp\tables::$table['provinzen'] .' p ON ub.provinz_id = p.id';

		return $sql;
	}

	/**
	* Load the data from the database for this unternehmen
	*
	* @param int $id unternehmen identifier
	* @return gebaude_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = static::get_sql_select($this->db_prefix).'
			WHERE '. $this->db->sql_in_set('ub.id', $id);
		$result = $this->db->sql_query($sql);
		$data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($data === false)
		{
			// A gebaude does not exist
			throw new \tacitus89\rsp_extension\exception\out_of_bounds('id');
		}

		//Import data
		$this->import($data);

		return $this;
	}

	/**
	* Get unternehmen_id
	*
	* @return int unternehmen_id
	* @access public
	*/
	public function get_unternehmen_id()
	{
		return $this->getInteger($this->data['unternehmen_id']);
	}

	/**
	* Set unternehmen_id
	*
	* @param int $unternehmen_id
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_unternehmen_id($unternehmen_id)
	{
		return $this->setInteger('unternehmen_id', $unternehmen_id);
	}

	/**
	* Get betrieb
	*
	* @return object betrieb
	* @access public
	*/
	public function get_betrieb()
	{
		return $this->data['betrieb_id'];
	}

	/**
	* Set betrieb
	*
	* @param int $betrieb_id
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_betrieb($betrieb_id)
	{
		// Enforce a integer
		$betrieb_id = (integer) $betrieb_id;

		// If the data is less than 0, it's not unsigned and we'll throw an exception
		if ($betrieb_id < 0)
		{
			throw new \tacitus89\gamesmod\exception\out_of_bounds('betrieb_id');
		}

		//Generated new games_cat object
		$this->data['betrieb_id'] = new betrieb($this->db, $this->db_prefix);

		//Load the data for new parent
		$this->data['betrieb_id']->load($betrieb_id);

		return $this;
	}

	/**
	* Get provinz_id
	*
	* @return int provinz_id
	* @access public
	*/
	public function get_provinz()
	{
		return $this->data['provinz_id'];
	}

	/**
	* Set provinz_id
	*
	* @param int $provinz_id
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_provinz_id($provinz_id)
	{
		// Enforce a integer
		$provinz_id = (integer) $provinz_id;

		// If the data is less than 0, it's not unsigned and we'll throw an exception
		if ($provinz_id < 0)
		{
			throw new \tacitus89\gamesmod\exception\out_of_bounds('provinz_id');
		}

		//Generated new games_cat object
		$this->data['provinz_id'] = new provinz($this->db, $this->db_prefix);

		//Load the data for new parent
		$this->data['provinz_id']->load($provinz_id);

		return $this;
	}

	/**
	* Get aktuelle_produktion
	*
	* @return int aktuelle_produktion
	* @access public
	*/
	public function get_aktuelle_produktion()
	{
		return $this->getInteger($this->data['aktuelle_produktion']);
	}

	/**
	* Set aktuelle_produktion
	*
	* @param int $aktuelle_produktion
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_aktuelle_produktion($aktuelle_produktion)
	{
		return $this->setInteger('aktuelle_produktion', $aktuelle_produktion);
	}

	/**
	* Get anzahl_produktion
	*
	* @return int anzahl_produktion
	* @access public
	*/
	public function get_anzahl_produktion()
	{
		return $this->getInteger($this->data['anzahl_produktion']);
	}

	/**
	* Set anzahl_produktion
	*
	* @param int $anzahl_produktion
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_anzahl_produktion($anzahl_produktion)
	{
		return $this->setInteger('anzahl_produktion', $anzahl_produktion);
	}
}
