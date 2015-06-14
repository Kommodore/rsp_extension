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
		'provinz_id'            => 'integer',
		'aktuelle_produktion'	=> 'integer',
		'anzahl_produktion'		=> 'integer',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array(
		'betrieb_id'			=> array('name' => 'betrieb',
										 'param' => array('db', 'betriebe_table')),
	);

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'unternehmen_id',
		'provinz_id',
		'aktuelle_produktion',
		'anzahl_produktion',
	);

	/**
	* The database table the betriebe are stored in
	*
	* @var string
	*/
	protected $betriebe_table;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $gebaude_table   Name of the table used to store gebaude data
	* @return \tacitus89\rsp_extension\entity\gebaude
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $unternehmen_betriebe_table, $betriebe_table)
	{
		$this->db = $db;
		$this->db_table = $unternehmen_betriebe_table;
		$this->betriebe_table = $betriebe_table;
	}

	/**
	* Generated a new Object
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $games_cat_table Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access protected
	*/
	protected static function factory($db, $unternehmen_betriebe_table, $betriebe_table)
	{
		return new self($db, $unternehmen_betriebe_table, $betriebe_table);
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
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'ub', 'betrieb_id' => 'b')) .'
			FROM ' . $this->db_table . ' ub
			LEFT JOIN '. $this->betriebe_table .' b ON ub.betrieb_id = b.id
			WHERE '. $this->db->sql_in_set('ub.id', $id);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			// A gebaude does not exist
			throw new \tacitus89\rsp_extension\exception\out_of_bounds('id');
		}

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
		$this->data['betrieb_id'] = new betrieb($this->db, $this->betriebe_table);

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
	public function get_provinz_id()
	{
		return $this->getInteger($this->data['provinz_id']);
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
		return $this->setInteger('provinz_id', $provinz_id);
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
