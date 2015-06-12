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
class ressourcen_bereich extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'name'                		=> 'string',
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
	* @param string                               $ressource_table   Name of the table used to store ressource data
	* @return \tacitus89\rsp_extension\entity\ressource
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $ressourcen_bereich_table)
	{
		$this->db = $db;
		$this->db_table = $ressourcen_bereich_table;
	}

	/**
	* Generated a new Object
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $games_cat_table Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access protected
	*/
	protected static function factory($db, $ressourcen_bereich_table)
	{
		return new self($db, $ressourcen_bereich_table);
	}

	/**
	* Load the data from the database for this ressource
	*
	* @param int $id ressource identifier
	* @return ressource_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'rb')) .'
			FROM ' . $this->$ressourcen_bereich_table . ' rb
			WHERE '. $this->db->sql_in_set('rb.id', $id);
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
	* Get name
	*
	* @return string name
	* @access public
	*/
	public function get_name()
	{
		return $this->getString($this->data['name']);
	}
}
