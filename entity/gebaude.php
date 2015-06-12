<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp_extension\entity;

/**
* Entity for a single gebaude
*/
class gebaude extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'name'                		=> 'string',
		'gueterbereich'             => 'integer',
		'produktion_id'             => 'integer',
		'max_stufen'                => 'integer',
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
		'gueterbereich',
		'produktion_id',
		'max_stufen',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $gebaude_table   Name of the table used to store gebaude data
	* @return \tacitus89\rsp_extension\entity\gebaude
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $gebaude_table)
	{
		$this->db = $db;
		$this->db_table = $gebaude_table;
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
	* Load the data from the database for this gebaude
	*
	* @param int $id gebaude identifier
	* @return gebaude_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'g')) .'
			FROM ' . $this->gebaude_info_table . ' g
			WHERE '. $this->db->sql_in_set('g.id', $id);
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
	* Get gebaude_id
	*
	* @return int gebaude_id
	* @access public
	*/
	public function get_gueterbereich()
	{
		return $this->getInteger($this->data['gueterbereich']);
	}

    /**
	* Get stufe
	*
	* @return int stufe
	* @access public
	*/
	public function get_produktion_id()
	{
		return $this->getInteger($this->data['produktion_id']);
	}

    /**
	* Get wert
	*
	* @return int wert
	* @access public
	*/
	public function get_max_stufen()
	{
		return $this->getInteger($this->data['max_stufen']);
	}
}
