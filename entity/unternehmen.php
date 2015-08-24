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
class unternehmen extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'     				=> 'integer',
		'user_id'          		=> 'integer',
		'name'		            => 'string',
		'logo_url'              => 'string',
		'gueterbereich'			=> 'integer',
		'anzahl_betriebe'		=> 'integer',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array();

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'user_id',
		'gueterbereich',
		'anzahl_betriebe',
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
		$sql = 'SELECT '. static::get_sql_fields(array('unternehmen' => 'u')) .'
			FROM ' . $db_prefix.\tacitus89\rsp\tables::$table['unternehmen'] . ' u';

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
			WHERE '. $this->db->sql_in_set('u.id', $id);
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
	* Load the data from the database for this unternehmen by name
	*
	* @param int $id unternehmen identifier
	* @return gebaude_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load_by_name($name)
	{
		$sql = static::get_sql_select($this->db_prefix).'
			WHERE '. $this->db->sql_in_set('u.name', $name);
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
	* Get user_id
	*
	* @return int user_id
	* @access public
	*/
	public function get_user_id()
	{
		return $this->getInteger($this->data['user_id']);
	}

	/**
	* Set user_id
	*
	* @param int $user_id
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_user_id($user_id)
	{
		return $this->setInteger('user_id', $user_id);
	}

	/**
	* Get name
	*
	* @return string name
	* @access public
	*/
	public function get_name()
	{
		return $this->getString($this->data['name']);
	}

	/**
	* Set name
	*
	* @param string $name
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_name($name)
	{
		return $this->setString('name', $name);
	}

	/**
	* Get logo_url
	*
	* @return string logo_url
	* @access public
	*/
	public function get_logo_url()
	{
		return $this->getString($this->data['logo_url']);
	}

	/**
	* Set logo_url
	*
	* @param string $logo_url
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_logo_url($logo_url)
	{
		return $this->setString('logo_url', $logo_url);
	}

	/**
	* Get gueterbereich
	*
	* @return int gueterbereich
	* @access public
	*/
	public function get_gueterbereich()
	{
		return $this->getInteger($this->data['gueterbereich']);
	}

	/**
	* Set gueterbereich
	*
	* @param int $gueterbereich
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_gueterbereich($gueterbereich)
	{
		return $this->setInteger('gueterbereich', $gueterbereich);
	}

    /**
	* Get anzahl_betriebe
	*
	* @return int anzahl_betriebe
	* @access public
	*/
	public function get_anzahl_betriebe()
	{
		return $this->getInteger($this->data['anzahl_betriebe']);
	}

	/**
	* Set anzahl_betriebe
	*
	* @param int $anzahl_betriebe
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_anzahl_betriebe($anzahl_betriebe)
	{
		return $this->setInteger('anzahl_betriebe', $anzahl_betriebe);
	}
}
