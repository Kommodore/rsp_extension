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
* Entity for a single ressource
*/
class user_ress extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'user_id'                	=> 'integer',
		'ress_id'                  	=> 'object',
		'menge' 		            => 'integer',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array(
		'ress_id'					=> 'ressource',
	);

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'user_id',
		'menge',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $db_prefix 	   The prefix of database table
	* @return \tacitus89\rsp_extension\entity\ressource
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
		$sql = 'SELECT '. static::get_sql_fields(array('user_ress' => 'ur', 'ressource' => 'r')) .'
			FROM ' . $db_prefix.\tacitus89\rsp\tables::$table['user_ress'] . ' ur
			LEFT JOIN '. $db_prefix.\tacitus89\rsp\tables::$table['ressourcen'] .' r ON r.id = ur.ress_id';

		return $sql;
	}

	/**
	* Load the data from the database for this ressource
	*
	* @param int $user_id user identifier
	* @return ressource_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = static::get_sql_select($this->db_prefix).'
			WHERE '. $this->db->sql_in_set('ur.user_id', $user_id);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			// A ressource does not exist
			throw new \tacitus89\rsp_extension\exception\out_of_bounds('user_id');
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
	* Get ress_id
	*
	* @return object ress
	* @access public
	*/
	public function get_ress()
	{
		return $this->data['ress_id'];
	}

	/**
	* Set ress
	*
	* @param int $ress_id
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_ress($user_id)
	{
		// Enforce a integer
		$user_id = (integer) $user_id;

		// If the data is less than 0, it's not unsigned and we'll throw an exception
		if ($user_id < 0)
		{
			throw new \tacitus89\gamesmod\exception\out_of_bounds('user_id');
		}

		//Generated new games_cat object
		$this->data['ress_id'] = new ressource($this->db, $this->ressourcen_table);

		//Load the data for new parent
		$this->data['ress_id']->load($user_id);

		return $this;
	}

	/**
	* Get menge
	*
	* @return int menge
	* @access public
	*/
	public function get_menge()
	{
		return $this->getInteger($this->data['menge']);
	}

	/**
	* Set menge
	*
	* @param int $menge
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\unexpected_value
	*/
	public function set_menge($menge)
	{
		return $this->setInteger('menge', $menge);
	}
}
