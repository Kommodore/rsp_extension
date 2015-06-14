<?php
/**
* @package RSP Extension for phpBB3.1
*
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\rsp\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Operator for a set of games_cat
*/
class unternehmen
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The database table the unternehmen for user are stored in
	*
	* @var string
	*/
	protected $unternehmen_table;

	/**
	* The database table the unternehmen_betriebe are stored in
	*
	* @var string
	*/
	protected $unternehmen_betriebe_table;

	/**
	* The database table the betriebe are stored in
	*
	* @var string
	*/
	protected $betriebe_table;

	/**
	* Constructor
	*
	* @param ContainerInterface $container		Service container interface
	* @param phpbb\db\driver\driver_interface 	$db
	* @param string							 	$game_table
	* @return \tacitus89\gamesmod\operators\game
	* @access public
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $unternehmen_table,  $unternehmen_betriebe_table, $betriebe_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->unternehmen_table = $unternehmen_table;
		$this->unternehmen_betriebe_table = $unternehmen_betriebe_table;
		$this->betriebe_table = $betriebe_table;
	}

	/**
	* Get the unternehmen of a user
	*
	* @param int $user_id
	* @return array Array of unternehmen data entities
	* @access public
	*/
	public function get_all_user_unternehmen($user_id)
	{
		$unternehmen = array();

		$sql= 'SELECT '. \tacitus89\rsp\entity\unternehmen::get_sql_fields(array('this' => 'u')) .'
			FROM ' . $this->unternehmen_table . ' u
			WHERE ' . $this->db->sql_in_set('u.user_id', $user_id) .'
			ORDER BY u.id ASC';
        $result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$unternehmen[] = $this->container->get('tacitus89.rsp.entity.unternehmen')
				->import($row);
		}
		$this->db->sql_freeresult($result);

		// Return all ressourcen entities
		return $unternehmen;
	}

	/**
	* Get the betriebe of a unternehmen
	*
	* @param int $unternehmen_id
	* @return array Array of unternehmen_betriebe data entities
	* @access public
	*/
	public function get_all_betriebe_of_unternehmen($unternehmen_id)
	{
		$betriebe = array();

		$sql= 'SELECT '. \tacitus89\rsp\entity\unternehmen_betrieb::get_sql_fields(array('this' => 'ub', 'betrieb_id' => 'b')) .'
			FROM ' . $this->unternehmen_betriebe_table . ' ub
			LEFT JOIN '. $this->betriebe_table .' b ON ub.betrieb_id = b.id
			WHERE ' . $this->db->sql_in_set('ub.unternehmen_id', $unternehmen_id) .'
			ORDER BY ub.id ASC';
        $result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$betriebe[] = $this->container->get('tacitus89.rsp.entity.unternehmen_betrieb')
				->import($row);
		}
		$this->db->sql_freeresult($result);

		// Return all ressourcen entities
		return $betriebe;
	}
}
