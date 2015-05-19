<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 24.03.14
 * Time: 17:42
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class Waren {

	private $id;
	private $name;
	private $url;
    //Die der aufrufende User hat
    private $menge;

	public function __construct($id, $name, $url, $menge)
	{
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->menge = $menge;
	}

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMenge()
    {
        return $this->menge;
    }

    public function reduceMenge($minus)
    {
        $this->menge = $this->menge - $minus;
    }

    public function setMenge($menge)
    {
        $this->menge = $menge;
    }

    public function getText()
    {
        return $this->menge . ' ' . $this->name . '<br />';
    }

} 