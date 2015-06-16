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
		'gebaude_id'                => 'object',
		'stufe'                     => 'integer',
		'wert'                      => 'integer',
		'bild_url'                  => 'string',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array(
		'gebaude_id'			=> 'gebaude',
	);

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'stufe',
		'wert',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $db_prefix	   The prefix of database table
	* @return \tacitus89\rsp_extension\entity\betrieb
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
		$sql = 'SELECT '. static::get_sql_fields(array('betrieb' => 'b', 'gebaude' => 'g')) .'
			FROM ' . $db_prefix.\tacitus89\rsp\tables::$table['betriebe'] . ' r
			LEFT JOIN '. $db_prefix.\tacitus89\rsp\tables::$table['gebaude'] .' g ON b.gebaude_id = g.id';

		return $sql;
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
		$sql = static::get_sql_select($this->db_prefix).'
			WHERE '. $this->db->sql_in_set('b.id', $id);
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
	* Get gebaude_id
	*
	* @return int gebaude_id
	* @access public
	*/
	public function get_gebaude()
	{
        return $this->data['gebaude_id'];
	}

    /**
	* Get stufe
	*
	* @return int stufe
	* @access public
	*/
	public function get_stufe()
	{
		return $this->getInteger($this->data['stufe']);
	}

    /**
	* Get wert
	*
	* @return int wert
	* @access public
	*/
	public function get_wert()
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
