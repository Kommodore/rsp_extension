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
* Entity for a single ressource
*/
class ressource
{
	/**
	* Data for this entity
	*
	* @var array
	*	id
	*	name
	*	url
	*	bereich_id
	* @access protected
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The database table the ressource are stored in
	*
	* @var string
	*/
	protected $ressource_table;

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
		$this->ressource_table = $ressource_table;
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
		$sql = 'SELECT r.id, r.name, r.url, r.bereich_id
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
	* Import data for this ressource
	*
	* Used when the data is already loaded externally.
	* Any existing data on this ressource is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array $data Data array, typically from the database
	* @return ressource_interface $this object for chaining calls; load()->set()->save()
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
        	'url'                     	=> 'string',
        	'bereich_id'                => 'integer',
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
			'bereich_id',
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
	* @return int ressource identifier
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
	* Get url
	*
	* @return string url
	* @access public
	*/
	public function get_url()
	{
		return (isset($this->data['url'])) ? (string) $this->data['url'] : '';
	}

	/**
	* Get bereich_id
	*
	* @return int bereich_id
	* @access public
	*/
	public function get_bereich_id()
	{
        return (isset($this->data['bereich_id'])) ? (int) $this->data['bereich_id'] : 0;
	}
}
