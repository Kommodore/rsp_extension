<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 26.03.14
 * Time: 15:33
 */

class Error {

    public static function fehlerMeldung($text, $link)
    {
        global $user, $phpbb_root_path, $phpEx;

        //Meldung
        $meta_url = append_sid("{$phpbb_root_path}rsp.$phpEx", $link);
        meta_refresh(5, $meta_url);
        $message = $text;
        $message .= '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid("{$phpbb_root_path}rsp.$phpEx", $link) . '">', '</a>');
        trigger_error($message);
    }

} 