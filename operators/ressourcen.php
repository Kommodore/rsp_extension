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
	* The prefix of database table
	*
	* @var string
	*/
	protected $db_prefix;

	/**
	* Constructor
	*
	* @param ContainerInterface $container		Service container interface
	* @param phpbb\db\driver\driver_interface 	$db
	* @param string							 	$db_prefix
	* @return \tacitus89\rsp\operators\game
	* @access public
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $db_prefix)
	{
		$this->container = $container;
		$this->db = $db;
		$this->db_prefix = $db_prefix;
	}

	/**
	* Get the ressourcen
	*
	* @param int $bereich_id
	* @return array Array of ressource data entities
	* @access public
	*/
	public function get_ressourcen_by_bereich($bereich_id)
	{
		$sql= \tacitus89\rsp\entity\ressource::get_sql_select($this->db_prefix) .'
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
	* @return array Array of ressource data entities
	* @access public
	*/
	public function get_all_ressourcen()
	{
		$ress = array();

		$sql=  \tacitus89\rsp\entity\ressource::get_sql_select($this->db_prefix) .'
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
	* Get all ressourcen of a user
	*
	* @param int $user_id
	* @return array Array of user_ress data entities
	* @access public
	*/
	public function get_all_user_ress($user_id)
	{
		$ress = array();

		$sql=  \tacitus89\rsp\entity\user_ress::get_sql_select($this->db_prefix) .'
			WHERE '. $this->db->sql_in_set('ur.user_id', $user_id) .'
			ORDER BY r.id';
        $result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ress[] = $this->container->get('tacitus89.rsp.entity.user_ress')
				->import($row);
		}
		$this->db->sql_freeresult($result);

		// Return all ressourcen entities
		return $ress;
	}

	/**
	* Get all necessary ressourcen of a gebaude
	*
	* @param int $gebaude_id
	* @return array Array of betrieb_rohstoff data entities
	* @access public
	*/
	public function get_ress_for_betrieb($gebaude_id)
	{
		$ress = array();

		$sql=  \tacitus89\rsp\entity\betrieb_rohstoff::get_sql_select($this->db_prefix) .'
			WHERE '. $this->db->sql_in_set('br.gebaude_id', $gebaude_id) .'
			ORDER BY r.id';
        $result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ress[] = $this->container->get('tacitus89.rsp.entity.betrieb_rohstoff')
				->import($row);
		}
		$this->db->sql_freeresult($result);

		// Return all ressourcen entities
		return $ress;
	}
}
