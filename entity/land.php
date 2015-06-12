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
* Entity for a single land
*/
class land extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'name'                  	=> 'string',
		'kurz_name'                 => 'string',
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
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $land_table   Name of the table used to store land data
	* @return \tacitus89\rsp_extension\entity\land
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $land_table)
	{
		$this->db = $db;
		$this->db_table = $land_table;
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
	* Load the data from the database for this land
	*
	* @param int $id land identifier
	* @return land_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'l')) .'
			FROM ' . $this->land_table . ' l
			WHERE '. $this->db->sql_in_set('l.id', $id);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			// A land does not exist
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
	public function get_kurz_name()
	{
		return $this->getString($this->data['kurz_name']);
	}
}
