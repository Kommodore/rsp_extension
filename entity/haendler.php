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
class haendler extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'ressource_id'              => 'integer',
		'preis'                  	=> 'integer',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array();

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'ressource_id',
		'preis',
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
		$sql = 'SELECT '. static::get_sql_fields(array('haendler' => 'h')) .'
			FROM ' . $db_prefix.\tacitus89\rsp\tables::$table['haendler'] . ' h';

		return $sql;
	}

	/** 
 	* Load the data from the database for this ressource
 	* 
 	* @param int $id ressource identifier 
	* @return haendler_interface $this object for chaining calls; load()->set()->save() 
 	* @access public 
 	* @throws \tacitus89\rsp_extension\exception\out_of_bounds 
 	*/ 
 	public function load($id) 
 	{ 
 		$sql = static::get_sql_select($this->db_prefix).' 
 			WHERE '. $this->db->sql_in_set('h.id', $id); 
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
	* Set Preis
	*
	* @param int $id The ressource identifier
	* @param int $preis The new Preis of the ressource
	* @return admin_interface $this object for chaining calls; load()->set()->save()
	* @access public
	*/
	public function set_preis($id,$preis)
	{

		// Set the description to our data array
		$this->data['ressource_id'] = $id;
		$this->data['preis'] = $preis;
		
		//Set the id from the subClasses
		$subClassesArray = array();
		foreach(static::$subClasses as $field => $value)
		{
			$subClassesArray[$field] = $this->data[$field];
			$this->data[$field] = $subClassesArray[$field]->get_id();
		}

		$sql = 'UPDATE '. $this->db_prefix.\tacitus89\rsp\tables::$table['haendler'] .'
			SET ' . $this->db->sql_build_array('UPDATE', $this->data) . '
			WHERE ressource_id = ' . $this->data['ressource_id'];
		$this->db->sql_query($sql);

		//Set the objects back to data
		foreach(static::$subClasses as $field => $value)
		{
			$this->data[$field] = $subClassesArray[$field];
		}

		return $this;
	}
}