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
class gebaude
{
	/**
	* Data for this entity
	*
	* @var array
	*	id
	*	name
	*	gueterbereich
	*	produktion_id
	*	max_stufen
	* @access protected
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The database table the gebaude are stored in
	*
	* @var string
	*/
	protected $gebaude_table;

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
		$this->gebaude_table = $gebaude_table;
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
		$sql = 'SELECT g.id, g.name, g.gueterbereich, g.produktion_id, g.max_stufen
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
	* Import data for this gebaude
	*
	* Used when the data is already loaded externally.
	* Any existing data on this gebaude is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array $data Data array, typically from the database
	* @return gebaude_interface $this object for chaining calls; load()->set()->save()
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
        	'name'                		=> 'string',
        	'gueterbereich'             => 'integer',
        	'produktion_id'             => 'integer',
        	'max_stufen'                => 'integer',
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
			'gueterbereich',
            'produktion_id',
            'max_stufen',
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
	* @return int gebaude identifier
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
	* Get gebaude_id
	*
	* @return int gebaude_id
	* @access public
	*/
	public function get_gueterbereich()
	{
        return (isset($this->data['gueterbereich'])) ? (int) $this->data['gueterbereich'] : 0;
	}

    /**
	* Get stufe
	*
	* @return int stufe
	* @access public
	*/
	public function get_produktion_id()
	{
        return (isset($this->data['produktion_id'])) ? (int) $this->data['produktion_id'] : 0;
	}

    /**
	* Get wert
	*
	* @return int wert
	* @access public
	*/
	public function get_max_stufen()
	{
        return (isset($this->data['max_stufen'])) ? (int) $this->data['max_stufen'] : 0;
	}
}
