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
		'name'                		=> 'string',
		'url'                     	=> 'string',
		'bereich_id'                => 'integer',
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
		'bereich_id',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $ressource_table   Name of the table used to store ressource data
	* @return \tacitus89\rsp_extension\entity\ressource
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $ressource_table)
	{
		$this->db = $db;
		$this->db_table = $ressource_table;
	}

	/**
	* Generated a new Object
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $games_cat_table Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access protected
	*/
	protected static function factory($db, $ressource_table)
	{
		return new self($db, $ressource_table);
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
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'r')) .'
			FROM ' . $this->ressource_table . ' r
			WHERE '. $this->db->sql_in_set('r.id', $id);
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

	/**
	* Get url
	*
	* @return string url
	* @access public
	*/
	public function get_url()
	{
		return $this->getString($this->data['url']);
	}

	/**
	* Get bereich_id
	*
	* @return int bereich_id
	* @access public
	*/
	public function get_bereich_id()
	{
		return $this->getInteger($this->data['bereich_id']);
	}
}
