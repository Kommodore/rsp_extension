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
* Entity for a single provinz
*/
class provinz extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'name'                  	=> 'string',
		'hstadt'					=> 'string',
		'land'                 		=> 'integer',
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
		'land',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $provinz_table   Name of the table used to store provinz data
	* @return \tacitus89\rsp_extension\entity\provinz
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $provinz_table)
	{
		$this->db = $db;
		$this->db_table = $provinz_table;
	}

	/**
	* Generated a new Object
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $games_cat_table Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access protected
	*/
	protected static function factory($db, $betrieb_table)
	{
		return new self($db, $betrieb_table);
	}

	/**
	* Load the data from the database for this provinz
	*
	* @param int $id provinz identifier
	* @return provinz_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'p')) .'
			FROM ' . $this->provinz_table . ' p
			WHERE '. $this->db->sql_in_set('p.id', $id);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			// A provinz does not exist
			throw new \tacitus89\rsp_extension\exception\out_of_bounds('id');
		}

		return $this;
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
	* Get kurz_name
	*
	* @return string kurz_name
	* @access public
	*/
	public function get_hstadt()
	{
		return $this->getString($this->data['hstadt']);
	}

	/**
	* Get land
	*
	* @return int land identifier
	* @access public
	*/
	public function get_land()
	{
		return $this->getInteger($this->data['land']);
	}
}
