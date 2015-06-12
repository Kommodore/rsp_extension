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
* Entity for a single betrieb
*/
class betrieb extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'gebaude_id'                => 'integer',
		'stufe'                     => 'integer',
		'wert'                      => 'integer',
		'bild_url'                  => 'string',
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
		'gebaude_id',
		'stufe',
		'wert',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $betrieb_table   Name of the table used to store betrieb data
	* @return \tacitus89\rsp_extension\entity\betrieb
	* @access public
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $betrieb_table)
	{
		$this->db = $db;
		$this->db_table = $betrieb_table;
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
	* Load the data from the database for this betrieb
	*
	* @param int $id betrieb identifier
	* @return betrieb_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT '. static::get_sql_fields(array('this' => 'b')) .'
			FROM ' . $this->db_table . ' b
			WHERE '. $this->db->sql_in_set('b.id', $id);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			// A betrieb does not exist
			throw new \tacitus89\rsp_extension\exception\out_of_bounds('id');
		}

		return $this;
	}

	/**
	* Get gebaude_id
	*
	* @return int gebaude_id
	* @access public
	*/
	public function getGebaudeId()
	{
        return $this->getInteger($this->data['gebaude_id']);
	}

    /**
	* Get stufe
	*
	* @return int stufe
	* @access public
	*/
	public function getStufe()
	{
		return $this->getInteger($this->data['stufe']);
	}

    /**
	* Get wert
	*
	* @return int wert
	* @access public
	*/
	public function getWert()
	{
		return $this->getInteger($this->data['wert']);
	}

	/**
	* Get bild
	*
	* @return string bild
	* @access public
	*/
	public function get_bild()
	{
		return $this->getString($this->data['bild_url']);
	}
}
