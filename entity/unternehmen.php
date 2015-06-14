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
	* @param string                               $gebaude_table   Name of the table used to store gebaude data
	* @return \tacitus89\rsp_extension\entity\gebaude
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $unternehmen_table)
	{
		$this->db = $db;
		$this->db_table = $unternehmen_table;
	}

	/**
	* Generated a new Object
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $games_cat_table Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access protected
	*/
	protected static function factory($db, $unternehmen_table)
	{
		return new self($db, $unternehmen_table);
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
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'u')) .'
			FROM ' . $this->db_table . ' u
			WHERE '. $this->db->sql_in_set('u.id', $id);
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
