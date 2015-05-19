<?php

class Ranking
{
    private $ress;
    private $ress_id;
    private $color = array('#ffe41f', //erster
        '#BF0000', '#BF0000', //2-3
        '#9d20b6', '#9d20b6', '#9d20b6',// 4-6
        '#1e40b8', '#1e40b8', '#1e40b8', '#1e40b8',// 7-10
    );

    public function __construct($ress_id=1)
    {
       $this->ress_id = $ress_id;
    }

    public function getRessList()
    {
        global $phpbb_root_path, $phpEx, $template, $db;

        $sql = 'SELECT id, name, url
			FROM ' . RSP_RESSOURCEN_TABLE;
        $result = $db->sql_query($sql);

        while($row = $db->sql_fetchrow($result))
        {
            $template->assign_block_vars('ress_block', array(
                'NAME'      => $row['name'],
                'IMG_URL'   => $phpbb_root_path.$row['url'],
                'URL'       => append_sid("{$phpbb_root_path}rsp.$phpEx", 'mode=ranking&i=' . $row['id']),
            ));
        }
    }

    public function getUserList()
    {
        global $template, $db;

        $sql = 'SELECT a.menge, a.user_id, b.username
			FROM ' . RSP_USER_RESS_TABLE . ' a
			LEFT JOIN ' . USERS_TABLE . ' b ON b.user_id = a.user_id
			WHERE ress_id = '. $this->ress_id .'
			ORDER BY a.menge DESC
			LIMIT 0 , 10';
        $result = $db->sql_query($sql);
        $num = 0;
        while($row = $db->sql_fetchrow($result))
        {
            $template->assign_block_vars('ranking_block', array(
                'MENGE'     => $row['menge'],
                'COLOR'     => $this->color[$num],
                'NAME'      => $row['username'],
                'URL'		=> get_username_string('profile', $row['user_id'], $row['username']),
            ));
            $num++;
        }
    }

}