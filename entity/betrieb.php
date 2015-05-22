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
* Entity for a single betrieb
*/
class betrieb
{
	/**
	* Data for this entity
	*
	* @var array
	*	id
	*	gebaude_id
	*	stufe
	*	wert
	*	bild_url
	* @access protected
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The database table the betrieb are stored in
	*
	* @var string
	*/
	protected $betrieb_table;

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
		$this->betrieb_table = $betrieb_table;
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
		$sql = 'SELECT b.id, b.gebaude_id, b.stufe, b.wert, b.bild_url
			FROM ' . $this->betrieb_table . ' b
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
	* Import data for this betrieb
	*
	* Used when the data is already loaded externally.
	* Any existing data on this betrieb is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array $data Data array, typically from the database
	* @return betrieb_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp_extension\exception\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = array();

		// All of our fields
		$fields = array(
			// column					=> data type (see settype())
            'id'                        => 'integer',
        	'gebaude_id'                => 'integer',
        	'stufe'                     => 'integer',
        	'wert'                      => 'integer',
        	'bild_url'                  => 'string',
		);

		// Go through the basic fields and set them to our data array
		foreach ($fields as $field => $type)
		{
			// If the data wasn't sent to us, throw an exception
			if (!isset($data[$field]))
			{
				throw new \tacitus89\rsp_extension\exception\invalid_argument(array($field, 'FIELD_MISSING'));
			}

			// If the type is a method on this class, call it
			if (method_exists($this, $type))
			{
				$this->$type($data[$field]);
			}
			else
			{
				// settype passes values by reference
				$value = $data[$field];

				// We're using settype to enforce data types
				settype($value, $type);

				$this->data[$field] = $value;
			}
		}

		// Some fields must be unsigned (>= 0)
		$validate_unsigned = array(
			'id',
			'gebaude_id',
            'stufe',
            'wert',
		);

		foreach ($validate_unsigned as $field)
		{
			// If the data is less than 0, it's not unsigned and we'll throw an exception
			if ($this->data[$field] < 0)
			{
				throw new \tacitus89\rsp_extension\exception\out_of_bounds($field);
			}
		}

		return $this;
	}

	/**
	* Get id
	*
	* @return int betrieb identifier
	* @access public
	*/
	public function get_id()
	{
		return (isset($this->data['id'])) ? (int) $this->data['id'] : 0;
	}

	/**
	* Get gebaude_id
	*
	* @return int gebaude_id
	* @access public
	*/
	public function get_gebaude_id()
	{
        return (isset($this->data['gebaude_id'])) ? (int) $this->data['gebaude_id'] : 0;
	}

    /**
	* Get stufe
	*
	* @return int stufe
	* @access public
	*/
	public function get_stufe()
	{
        return (isset($this->data['stufe'])) ? (int) $this->data['stufe'] : 0;
	}

    /**
	* Get wert
	*
	* @return int wert
	* @access public
	*/
	public function get_wert()
	{
        return (isset($this->data['wert'])) ? (int) $this->data['wert'] : 0;
	}

	/**
	* Get bild
	*
	* @return string bild
	* @access public
	*/
	public function get_bild()
	{
		return (isset($this->data['bild_url'])) ? (string) $this->data['bild_url'] : '';
	}
}
