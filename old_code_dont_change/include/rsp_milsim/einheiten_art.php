<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 26.03.14
 * Time: 14:47
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class EinheitenArt {
    protected $id;
    protected $name;

    //Betrieb mit bekannter ID
    public function __construct($info)
    {
        $this->id = $info['id'];
        $this->name = $info['name'];
    }
} 