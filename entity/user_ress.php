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
class ressource extends abstractEntity
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
		'ress_id'					=> array('name' => 'ressource',
											 'param' => array('db', 'ressourcen_table')),)
	);

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'user_id',
		'ress_id',
		'menge',
	);

	$ressourcen_table

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $ressource_table   Name of the table used to store ressource data
	* @return \tacitus89\rsp_extension\entity\ressource
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $user_ress_table, $ressourcen_table)
	{
		$this->db = $db;
		$this->db_table = $user_ress_table;
		$this->ressourcen_table = $ressourcen_table;
	}

	/**
	* Generated a new Object
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $games_cat_table Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access protected
	*/
	protected static function factory($db, $user_ress_table, $ressource_table)
	{
		return new self($db, $user_ress_table, $ressource_table);
	}

	/**
	* Load the data from the database for this ressource
	*
	* @param int $id ressource identifier
	* @return ressource_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'ur', 'ress_id' => 'r')) .'
			FROM ' . $this->db_table . ' ur
			LEFT JOIN '. $this->ressourcen_table .' r
			WHERE '. $this->db->sql_in_set('ur.id', $id);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			// A ressource does not exist
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
		if ($parent < 0)
		{
			throw new \tacitus89\gamesmod\exception\out_of_bounds($parent);
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
	public function set_user_id($menge)
	{
		return $this->setInteger('menge', $menge);
	}
}
