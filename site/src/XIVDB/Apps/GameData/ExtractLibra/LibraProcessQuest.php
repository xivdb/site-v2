<?php

/**
 * LibraProcessQuest
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessQuest extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_quests';

        $insert = [];
        $classjobs = [];
        $prequest = [];
        $postquest = [];
        $enpcs = [];
        $items = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new = [
                /*'genre'                      => $d['Genre'],
                'area'                       => $d['Area'],
                'company_point_type'            => $d['CompanyPointType'],
                'company_point_value'           => $d['CompanyPointNum'],
                'reward_gil'                    => $d['Gil'],
                'reward_exp_bonus'              => $d['ExpBonus'],
                'reward_exp'                    => isset($json['exp']) ? $json['exp'] : null,
                'npc_start'                     => $d['Client'],
                'header'                        => $d['Header'],
                'class_level'                   => $d['ClassLevel'],
                'class_level_2'                 => $d['ClassLevel2'],
                'classjob_category_1'        => $d['ClassJob'],
                'classjob_category_2'        => $d['ClassJob2'],
                'level_offset'                  => $d['QuestLevelOffset'],
                'beast_tribe'                => $d['BeastTribe'],
                'beast_tribe_rep'               => $d['BeastReputationValueNum'],
                'webtype'                    => $d['WebType'],
                'item_array_type'               => isset($json['ItemArrayType']) ? $json['ItemArrayType'] : null,
                'sort'                          => $d['Sort'],
                */
                'lodestone_type'                => $lodestone[0],
                'lodestone_id'                  => $lodestone[1],

            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }

            if (isset($json['classjob']))
            {
                foreach($json['classjob'] as $cj)
                {
                    $classjobs[] = [
                        'quest' => $id,
                        'classjob_category' => $cj['ClassJobCategory'],
                        'level' => $cj['level'],
                        'patch' => $this->patch
                    ];

                    if (count($classjobs) == 500)
                    {
                        //$this->insert('xiv_quests_to_classjob_category', $classjobs);
                        $classjobs = [];
                    }
                }
            }

            if (isset($json['PrevQuest']))
            {
                foreach($json['PrevQuest'] as $prevQuestId)
                {
                    $prevquests[] = [
                        'quest' => $id,
                        'quest_pre' => $prevQuestId,
                        'patch' => $this->patch
                    ];

                    if (count($prevquests) == 500)
                    {
                        //$this->insert('xiv_quests_to_pre_quest', $prevquests);
                        $prevquests = [];
                    }
                }
            }

            if (isset($json['PostQuest']))
            {
                foreach($json['PostQuest'] as $prevQuestId)
                {
                    $postquest[] = [
                        'quest' => $id,
                        'quest_post' => $prevQuestId,
                        'patch' => $this->patch
                    ];

                    if (count($postquest) == 500)
                    {
                        //$this->insert('xiv_quests_to_post_quest', $postquest);
                        $postquest = [];
                    }
                }
            }

            if (isset($json['OptionalItem']))
            {
                foreach($json['OptionalItem'] as $item)
                {
                    $itemData = reset($item);
                    $itemId = key($item);

                    $items[] = [
                        'quest'  => $id,
                        'item'   => $itemId,
                        'rgb'       => isset($itemData['stain']) ? implode(',', $itemData['stain']) : null,
                        'quantity'  => $itemData['num'],
                        'optional'  => 1,
                        'patch'     => $this->patch,
                    ];

                    if (count($items) == 500)
                    {
                        //$this->insert('xiv_quests_to_rewards', $items);
                        $items = [];
                    }
                }
            }

            if (isset($json['Item']))
            {
                foreach($json['Item'] as $item)
                {
                    $itemData = reset($item);
                    $itemId = key($item);

                    $items[] = [
                        'quest'  => $id,
                        'item'   => $itemId,
                        'rgb'       => isset($itemData['stain']) ? implode(',', $itemData['stain']) : null,
                        'quantity'  => $itemData['num'],
                        'optional'  => 0,
                        'patch'     => $this->patch,
                    ];

                    if (count($items) == 500)
                    {
                        //$this->insert('xiv_quests_to_rewards', $items);
                        $items = [];
                    }
                }
            }
        }

        $this->insert($table, $insert);
        //$this->insert('xiv_quests_to_classjob_category', $classjobs);
        //$this->insert('xiv_quests_to_pre_quest', $prevquests);
        //$this->insert('xiv_quests_to_post_quest', $postquest);
        //$this->insert('xiv_quests_to_rewards', $items);

        return true;
    }
}
