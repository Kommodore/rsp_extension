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
class ressourcen
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* The database table the ressourcen are stored in
	*
	* @var string
	*/
	protected $ressourcen_table;

	/**
	* Constructor
	*
	* @param ContainerInterface $container		Service container interface
	* @param phpbb\db\driver\driver_interface 	$db
	* @param string							 	$game_table
	* @return \tacitus89\gamesmod\operators\game
	* @access public
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $ressourcen_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->ressourcen_table = $ressourcen_table;
	}

	/**
	* Get the ressourcen
	*
	* @param int $bereich_id
	* @return array Array of game data entities
	* @access public
	*/
	public function get_ressourcen_by_bereich($bereich_id)
	{
		$sql= 'SELECT '. \tacitus89\rsp\entity\ressource::get_sql_fields(array('this' => 'r')) .'
			FROM ' . $this->ressourcen_table . ' r
			WHERE ' . $this->db->sql_in_set('r.bereich_id', $bereich_id) .'
			ORDER BY r.id ASC';
        $result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ress[] = $this->container->get('tacitus89.rsp.entity.ressource')
				->import($row);
		}
		$this->db->sql_freeresult($result);

		// Return all ressourcen entities
		return $ress;
	}

	/**
	* Get all ressourcen
	*
	* @param int $bereich_id
	* @return array Array of game data entities
	* @access public
	*/
	public function get_all_ressourcen()
	{
		$sql= 'SELECT '. \tacitus89\rsp\entity\ressource::get_sql_fields(array('this' => 'r')) .'
			FROM ' . $this->ressourcen_table . ' r
			ORDER BY r.id ASC';
        $result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ress[] = $this->container->get('tacitus89.rsp.entity.ressource')
				->import($row);
		}
		$this->db->sql_freeresult($result);

		// Return all ressourcen entities
		return $ress;
	}
}
