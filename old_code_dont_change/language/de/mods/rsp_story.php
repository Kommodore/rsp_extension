<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 08.09.14
 * Time: 17:02
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
    $lang = array();
}
// Merge language entries into the common lang array
$lang = array_merge($lang, array(
    'STORY'		=> 'Story',
));
?>