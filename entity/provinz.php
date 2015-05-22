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
* Entity for a single provinz
*/
class provinz
{
	/**
	* Data for this entity
	*
	* @var array
	*	id
	*	name
	*	hstadt
	*	provinz
	* @access protected
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The database table the provinz are stored in
	*
	* @var string
	*/
	protected $provinz_table;

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
		$this->provinz_table = $provinz_table;
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
		$sql = 'SELECT p.id, p.name, p.hstadt, p.provinz
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
	* Import data for this provinz
	*
	* Used when the data is already loaded externally.
	* Any existing data on this provinz is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array $data Data array, typically from the database
	* @return provinz_interface $this object for chaining calls; load()->set()->save()
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
			'name'                  	=> 'string',
			'hstadt'					=> 'string',
        	'land'                 		=> 'integer',
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
			'land',
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
	* @return int provinz identifier
	* @access public
	*/
	public function get_id()
	{
		return (isset($this->data['id'])) ? (int) $this->data['id'] : 0;
	}

	/**
	* Get name
	*
	* @return string name
	* @access public
	*/
	public function get_name()
	{
		return (isset($this->data['name'])) ? (string) $this->data['name'] : '';
	}

	/**
	* Get kurz_name
	*
	* @return string kurz_name
	* @access public
	*/
	public function get_hstadt()
	{
		return (isset($this->data['hstadt'])) ? (string) $this->data['hstadt'] : '';
	}

	/**
	* Get land
	*
	* @return int land identifier
	* @access public
	*/
	public function get_land()
	{
		return (isset($this->data['land'])) ? (int) $this->data['land'] : 0;
	}
}
