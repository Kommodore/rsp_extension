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
}
