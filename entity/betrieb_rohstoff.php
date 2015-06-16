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
* Entity for a single ressource
*/
class betrieb_rohstoff extends abstractEntity
{
	/**
	* All of fields of this objects
	*
	**/
	protected static $fields = array(
		'id'                        => 'integer',
		'gebaude_id'           		=> 'integer',
		'ressourcen_id'            	=> 'object',
		'menge'		                => 'integer',
	);

	/**
	* All object must be assigned to a class
	**/
	protected static $subClasses = array(
		'ressourcen_id'				=> 'ressource',
	);

	/**
	* Some fields must be unsigned (>= 0)
	**/
	protected static $validate_unsigned = array(
		'id',
		'gebaude_id',
		'menge',
	);

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db              Database object
	* @param string                               $db_prefix	   The prefix of database table
	* @return \tacitus89\rsp_extension\entity\ressource
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
		$sql = 'SELECT '. static::get_sql_fields(array('betrieb_rohstoff' => 'br', 'ressource' => 'r')) .'
			FROM ' . $db_prefix.\tacitus89\rsp\tables::$table['betriebe_rohstoffe'] . ' br
			LEFT JOIN ' . $db_prefix.\tacitus89\rsp\tables::$table['ressourcen'] . ' r ON r.id = br.ressourcen_id';

		return $sql;
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
		$sql = static::get_sql_select($this->db_prefix).'
			WHERE '. $this->db->sql_in_set('br.id', $id);
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
	* @return string gebaude_id
	* @access public
	*/
	public function get_gebaude_id()
	{
		return $this->getString($this->data['gebaude_id']);
	}

	/**
	* Get ressource
	*
	* @return string ressource
	* @access public
	*/
	public function get_ressource()
	{
		return $this->data['ressourcen_id'];
	}

	/**
	* Get menge
	*
	* @return int menge
	* @access public
	*/
	public function get_menge()
	{
		return $this->getInteger($this->data['menge']);
	}
}
