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
* Entity for the changelog
*/
class changelog extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'time'                  	=> 'integer',
		'text'						=> 'string',
		'text_uid'                 	=> 'string',
		'text_bitfield'				=> 'string',
		'text_options'				=> 'integer',
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
		'time',
		'text_options',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $db_prefix	   The prefix of database table
	* @return \tacitus89\rsp_extension\entity\changelog
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
		$sql = 'SELECT '. static::get_sql_fields(array('changelog' => 'cl')) .'
			FROM ' . $db_prefix.\tacitus89\rsp\tables::$table['changelog'] . ' cl';

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
	* Insert the changelog content for the first time
	*
	* Will throw an exception if the entry was already inserted (call save() instead)
	*
	* @return admin_interface $this object for chaining calls; load()->set()->save()
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

		// Insert the game data to the database
		$sql = 'INSERT INTO '. $this->db_prefix.\tacitus89\rsp\tables::$table['changelog'] .' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the id using the id created by the SQL insert
		$this->data['id'] = (int) $this->db->sql_nextid();

		return $this;
	}
	
	/**
	* Delete a changelog entry from the database
	*
	* @param int $id changelog identifier
	* @return null
	* @access public
	* @throws \tacitus89\rsp_extension\exception\out_of_bounds
	*/
	public function delete($id){
		
		$sql = static::get_sql_select($this->db_prefix).'
			WHERE '. $this->db->sql_in_set('cl.id', $id);
		$result = $this->db->sql_query($sql);
		$data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($data === false)
		{
			// This changelog entry does not exist
			throw new \tacitus89\rsp\exception\out_of_bounds('id');
		}

		$sql = 'DELETE FROM '. $this->db_prefix.\tacitus89\rsp\tables::$table['changelog'] .' WHERE '. $this->db->sql_in_set('id', $id);
		$result = $this->db->sql_query($sql);
	}
	
	/**
	* Set text
	*
	* @param string $text
	* @return admin_interface $this object for chaining calls; load()->set()->save()
	* @access public
	*/
	public function set_text($text)
	{
		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);

		// Set the description to our data array
		$this->data['text'] = $text;
		$this->data['text_uid'] = $uid;
		$this->data['text_bitfield'] = $bitfield;
		$this->data['text_options'] = $flags;
		$this->data['time'] = time();
		// Flags are already set

		return $this;
	}
}