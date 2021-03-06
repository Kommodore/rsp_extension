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
* Abstract Entity for all Entities
*/
abstract class abstractEntity
{
	/**
	* Data for this entity
	* @access protected
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The prefix of database table
	*
	* @var string
	*/
	protected $db_prefix;

	/**
	* All of fields of this objects
	*
	**/
	protected static $fields;

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses;

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned;

	/**
	* Generated from entity attribute the sql column
	*
	* @param array $table_prefix declare the prefix of tables
	* @return string The finished sql column
	* @access public
	* @throws \tacitus89\rsp\exception\out_of_bounds
	*/
	public static function get_sql_fields($table_prefix = array())
	{
		//get fields data
		$fields = static::$fields;

		//declare new fields
		$new_fields = array();

		//get the called class
		$called_class = substr(get_called_class(), strrpos(get_called_class(), '\\')+1);
		if(!empty($table_prefix))
		{
			//Go through all fields and renamed it
			foreach ($fields as $key => $value)
			{
				//If value a object
				if($value === 'object')
				{
					//get class of object
					$class = __NAMESPACE__. '\\' .static::$subClasses[$key];

					if(!isset($table_prefix[$called_class]))
					{
						//if object have subobject, it must be set a table_prefix
						throw new \tacitus89\rsp\exception\invalid_argument(array($key, 'FIELD_MISSING'));
					}

					//get the fields of the object
					$new_fields[] = $class::get_sql_fields($table_prefix);
				}
				$new_fields[] = $table_prefix[$called_class] .'.'. $key .' AS '. $called_class .'_'. $key;
			}
		}
		else
		{
			//Go through all fields and renamed it
			foreach ($fields as $key => $value)
			{
				if($value === 'object')
				{
					//if object have subobject, it must be set a table_prefix
					throw new \tacitus89\rsp\exception\invalid_argument(array($key, 'FIELD_MISSING'));
				}
				$new_fields[] = $key .' AS '. $called_class .'_'. $key;
			}
		}

		return implode(", ", $new_fields);
	}

	/**
	* Load the data from the database for this game
	*
	* @param int $id game identifier
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\out_of_bounds
	*/
	abstract public function load($id);

	/**
	* Import data for this game
	*
	* Used when the data is already loaded externally.
	* Any existing data on this game is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array $data Data array, typically from the database
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = array();

		//get class name
		$class = substr(get_called_class(), strrpos(get_called_class(), '\\')+1) .'_';

		// Go through the basic fields and set them to our data array
		foreach (static::$fields as $field => $type)
		{
			// If the data wasn't sent to us, throw an exception
			if (!isset($data[$class.$field]))
			{
				throw new \tacitus89\rsp\exception\invalid_argument(array($field, 'FIELD_MISSING'));
			}

			// If the type is a method on this class, call it
			if (method_exists($this, $type))
			{
				$this->$type($data[$class.$field]);
			}
			//Special case: if type a object!
			elseif($type === 'object')
			{
				//Get subclass
				$subclass = __NAMESPACE__. '\\' .static::$subClasses[$field];

				//Generating the subclass
				$this->data[$field] = new $subclass($this->db, $this->db_prefix);

				//Import the data to subclass
				$this->data[$field]->import($data);
			}
			else
			{
				// settype passes values by reference
				$value = $data[$class.$field];

				// We're using settype to enforce data types
				settype($value, $type);

				$this->data[$field] = $value;
			}
		}

		foreach (static::$validate_unsigned as $field)
		{
			// If the data is less than 0, it's not unsigned and we'll throw an exception
			if ($this->data[$field] < 0)
			{
				throw new \tacitus89\rsp\exception\out_of_bounds($field);
			}
		}

		return $this;
	}

	/**
	* Insert the game for the first time
	*
	* Will throw an exception if the game was already inserted (call save() instead)
	*
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\out_of_bounds
	*/
	public function insert()
	{
		if (!empty($this->data['id']))
		{
			// The game already exists
			throw new \tacitus89\rsp\exception\out_of_bounds('id');
		}

		// Make extra sure there is no id set
		unset($this->data['id']);

		//Set the id from the subClasses
		$subClassesArray = array();
		foreach(static::$subClasses as $field => $value)
		{
			$subClassesArray[$field] = $this->data[$field];
			$this->data[$field] = $subClassesArray[$field]->get_id();
		}

		// Insert the game data to the database
		$sql = 'INSERT INTO ' . $this->db_table . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the game_id using the id created by the SQL insert
		$this->data['id'] = (int) $this->db->sql_nextid();

		//Set the objects back to data
		foreach(static::$subClasses as $field => $value)
		{
			$this->data[$field] = $subClassesArray[$field];
		}

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding a game (saving for the first time), you must call insert() or an exeception will be thrown
	*
	* @return game_interface $this object for chaining calls; load()->set()->save()
	* @access public
	* @throws \tacitus89\rsp\exception\out_of_bounds
	*/
	public function save()
	{
		if (empty($this->data['id']))
		{
			// The game does not exist
			throw new \tacitus89\rsp\exception\out_of_bounds('id');
		}

		//Set the id from the subClasses
		$subClassesArray = array();
		foreach(static::$subClasses as $field => $value)
		{
			$subClassesArray[$field] = $this->data[$field];
			$this->data[$field] = $subClassesArray[$field]->get_id();
		}

		$sql = 'UPDATE ' . $this->db_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $this->data) . '
			WHERE id = ' . $this->get_id();
		$this->db->sql_query($sql);

		//Set the objects back to data
		foreach(static::$subClasses as $field => $value)
		{
			$this->data[$field] = $subClassesArray[$field];
		}

		return $this;
	}

	/**
	* Get id
	*
	* @return int game identifier
	* @access public
	*/
	public function get_id()
	{
		return (isset($this->data['id'])) ? (int) $this->data['id'] : 0;
	}

	/**
	* Get String for output
	*
	* @param string $string Get the string for output
	* @return string
	* @access protected
	*/
	protected function getString($string)
	{
		return (isset($string)) ? (string) $string : '';
	}

	/**
	* Set a string to data
	*
	* @param string $varname Name of variable in data array
	* @param string $string New value of $varname
	* @param integer $characters Allowed number of characters; Default: 255
	* @return page_interface $this object for chaining calls; load()->set()->save()
	* @access protected
	*/
	protected function setString($varname, $string, $characters = 255)
	{
		// Enforce a string
		$string = (string) $string;
		$varname = (string) $varname;

		// We limit the name length to $characters characters
		if (truncate_string($string, $characters) != $string)
		{
			throw new \tacitus89\rsp\exception\unexpected_value(array($varname, 'TOO_LONG'));
		}

		// Set the name on our data array
		$this->data[$varname] = $string;

		return $this;
	}

	/**
	* Get Integer for output
	*
	* @param string $integer Get the Integer for output
	* @return Integer
	* @access protected
	*/
	protected function getInteger($integer)
	{
		return (isset($integer)) ? (int) $integer : 0;
	}

	/**
	* Set a Integer to data
	*
	* @param string $varname Name of variable in data array
	* @param integer $integer New value of $varname
	* @param boolean $unsigned If must the integer unsigned?; Default: true
	* @return page_interface $this object for chaining calls; load()->set()->save()
	* @access protected
	*/
	protected function setInteger($varname, $integer, $unsigned = true)
	{
		// Enforce a integer
		$integer = (integer) $integer;
		// Enforce a string
		$varname = (string) $varname;

		// If the data is less than 0, it's not unsigned and we'll throw an exception
		if ($unsigned && $integer < 0)
		{
			throw new \tacitus89\rsp\exception\out_of_bounds($integer);
		}

		// Set the order_id on our data array
		$this->data[$integer] = $integer;

		return $this;
	}

}
