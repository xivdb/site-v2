<?php

/**
 * LibraProcessAchievement
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessAchievement extends CoreLibra
{
    protected function handle()
    {
        $achievements = [];
        $achievementsPost = [];
        $achievementsPre = [];
        $achievementsPreQuest = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            // achievement
            $achievements[] = [
                'id' => $id,
                'category' => $d['Category'],
                'name_ja' => $d['Name_ja'],
                'name_en' => $d['Name_en'],
                'name_fr' => $d['Name_fr'],
                'name_de' => $d['Name_de'],

                'help_ja' => $d['Help_ja'],
                'help_en' => $d['Help_en'],
                'help_fr' => $d['Help_fr'],
                'help_de' => $d['Help_de'],
                'help_cns' => null,
                'points' => $d['Point'],
                'icon' => $d['Icon'],
                'reward_item' => $d['Item'],
                'reward_title' => $d['Title'],
                'priority' => $d['Priority'],
                'lodestone_type' => $lodestone[0],
                'lodestone_id' => $lodestone[1],
                'patch' => $this->patch,
            ];


            // Post achievements
            if (isset($json['PostAchievement']))
            {
                foreach($json['PostAchievement'] as $a)
                {
                    $achievementsPost[] = [
                        'achievement' => $id,
                        'post_achievement' => $a,
                        'patch' => $this->patch,
                    ];
                }
            }

            // Pre achievements
            if (isset($json['condition_achievement']))
            {
                foreach($json['condition_achievement'] as $a)
                {
                    $achievementsPre[] = [
                        'achievement' => $id,
                        'pre_achievement' => $a,
                        'patch' => $this->patch,
                    ];
                }
            }

            // Pre quests
            if (isset($json['condition_quest']))
            {
                foreach($json['condition_quest'] as $type => $a)
                {
                    $type = ($type == 'OR') ? 1 : 0;

                    foreach($a as $b)
                    {
                        $achievementsPreQuest[] = [
                            'achievement' => $id,
                            'quest' => $b,
                            'is_or' => $type,
                            'patch' => $this->patch,
                        ];
                    }
                }
            }
        }

        $this->insert('xiv_achievements', $achievements);
        $this->insert('xiv_achievements_post', $achievementsPost);
        $this->insert('xiv_achievements_pre', $achievementsPre);
        $this->insert('xiv_achievements_pre_quests', $achievementsPreQuest);

        return true;
    }
}

