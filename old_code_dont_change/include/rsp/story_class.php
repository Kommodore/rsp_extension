<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 08.09.14
 * Time: 17:04
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
    exit;
}

class Story {
    private $info;

    public function __construct()
    {
        $this->info['id'] = request_var('i', 0);
        $this->info['part'] = request_var('p', 1);

        if($this->info['id'] == 0)
        {
            $this->getAllMoeglicheStory();
        }
        else
        {
            $this->getStory();
        }
    }

    private function getAllMoeglicheStory()
    {
        global $db, $user, $template;
        global $phpbb_root_path, $phpEx;

        $sql = 'SELECT s.id, s.uberschrift, u.status, u.part_id
			FROM ' . RSP_STORY_TABLE . ' s
			LEFT JOIN '. RSP_STORY_USER_TABLE .' u ON u.story_id = s.id AND u.status < 2
			WHERE s.id NOT IN (SELECT story_id FROM '. RSP_STORY_USER_TABLE .' WHERE user_id = '. $user->data['user_id'] .' AND status = 2)';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            $this->info['id'] = $row['id'];
            $this->info['uberschrift'] = $row['uberschrift'];
            $this->info['part_id'] = ($row['part_id'] != NULL)? $row['part_id']: 0;
            $this->info['status'] = ($row['status'] === 1)? 1: 0;

            $template->assign_block_vars('storyrow', array(
                'ID'            => $this->info['id'],
                'UBERSCHRIFT'   => $this->info['uberschrift'],
                'START'        => ($this->info['status'] === 0)? TRUE : FALSE,
                'LINK'          => ($this->info['status'] == 0)? append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=story&amp;i=". $this->info['id']) : append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=story&amp;i=". $this->info['id'] ."&amp;p=". $this->info['part_id']) ,
            ));
        }
        $db->sql_freeresult($result);

        $template->assign_vars(array(
            'S_STORY_WAHL'	=> true,
        ));
    }

    private function getStory()
    {
        global $db, $user, $template;
        global $phpbb_root_path, $phpEx;

        $sql = 'SELECT p.uberschrift, p.id, p.text, o.uberschrift AS optionUberschrift, o.id AS optionId, a.text AS actionText, o.wert, o.next_part
			FROM '. RSP_STORY_PART_TABLE.' p
			INNER JOIN '. RSP_STORY_OPTIONS_TABLE .' o ON o.part_id = p.id
			LEFT JOIN '. RSP_STORY_ACTIONS_TABLE .' a ON a.id = o.action_id
			WHERE p.part = '. $this->info['part'] .'
			    AND p.story_id = '. $this->info['id'];
        $result = $db->sql_query($sql);

        while($row = $db->sql_fetchrow($result))
        {
            $this->info['part'] = array('id'            => $row['id'],
                                        'uberschrift'   => $row['uberschrift'],
                                        'text'          => $row['text'],);

            $this->info['part']['option'][] = array('id'            => $row['optionId'],
                                                    'uberschrift'   => $row['optionUberschrift'],
                                                    'actionText'    => $row['actionText'],
                                                    'wert'          => $row['wert'],
                                                    'next_part'     => $row['next_part'],
            );

            $template->assign_block_vars('option_block', array(
                'ID'	        => $row['optionId'],
                'UBERSCHRIFT'   => $row['optionUberschrift'],
                'ACTION'        => ($row['wert'] > 0)? $row['actionText'] . $row['wert'] : $row['actionText'],
                'LINK'          => append_sid("{$phpbb_root_path}rsp.$phpEx", "mode=story&amp;i=". $this->info['id'] ."&amp;p=". $row['next_part']),
            ));
        }

        $template->assign_vars(array(
            'S_STORY'	                => true,
            'L_STORY_PART_UBERSCHRIFT'  => $this->info['part']['uberschrift'],
            'L_STORY_PART_TEXT'         => $this->info['part']['text'],
        ));
    }
} 