<?php

/**
 * LibraProcessInstanceContent
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessInstanceContent extends CoreLibra
{
    private $tomestones = [
        'A' => 26, 'B' => 28,
    ];

    protected function handle()
    {
        $table = 'xiv_instances';

        $insert = [];
        $chests = [];
        $bosses = [];
        $items = [];
        $currency = [];
        $enemies = [];
        $rewards = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new = [
                /*'help_ja' => $d['Description_ja'],
                'help_en' => $d['Description_en'],
                'help_fr' => $d['Description_fr'],
                'help_de' => $d['Description_de'],
                'help_cns' => null,

                'sort'                      => $d['Sortkey'],
                'type'                      => $d['Type'],
                'level_min'                 => $d['LevelMin'],
                'level_max'                 => $d['LevelMax'],
                'time_limit'                => $d['Time'],
                'halfway'                   => $d['Halfway'],
                'random_content_type'       => $d['RandomContentType'],
                'alliance'                  => $d['Alliance'],
                'party_finder_condition'    => $d['FinderPartyCondition'],

                'players_per_party'               => $d['PartyMemberCount'],
                'tanks_per_party'                 => $d['TankCount'],
                'healers_per_party'               => $d['HealerCount'],
                'melees_per_party'              => $d['AttackerCount'],
                'ranged_per_party'                 => $d['RangeCount'],
                'differentiates_dps'        => $d['DifferentiateDPS'],
                'party_count'               => $d['PartyCount'],
                'free_role'                 => $d['FreeRole'],
                'item_level'                => $d['ItemLevel'],
                'item_level_sync'            => $d['ItemLevelMax'],
                'colosseum'                 => $d['Colosseum'],

                'force_count'               => $d['ForceCount'],*/

                 'placename'                      => $d['Area'],

                'is_echo_default'           => $d['is_koeru_usually'],
                'is_echo_annihilation'      => $d['is_koeru_annihilation'],

                'lodestone_type'            => $lodestone[0],
                'lodestone_id'              => $lodestone[1],
            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }

            // chests
            if (isset($json['Map']))
            {
                foreach($json['Map'] as $i => $mapdata)
                {
                    $x = $mapdata['coordinate'][0];
                    $y = $mapdata['coordinate'][1];
                    $week_restriction = $mapdata['WeekRestrictionIndex'];

                    foreach($mapdata['Item'] as $itemId)
                    {
                        $chests[] =
                        [
                            'instance' => $id,
                            'chest_num' => $i,
                            'item' => $itemId,
                            'week_restriction' => $week_restriction,
                            'info' => 'map',
                            'boss' => null,
                            'x' => $x, 'y' => $y,
                            'patch' => $this->patch,
                        ];

                        if (count($chests) == 500)
                        {
                            $this->insert('xiv_instances_chests_to_items', $chests);
                            $chests = [];
                        }
                    }
                }
            }

            // boss
            if (isset($json['Boss']))
            {
                // if first boss
                if (isset($json['Boss']))
                {
                    $mainBossId = null;
                    $boss = $json['Boss'];

                    // add bosses
                    foreach($boss['BNpcNames'] as $bossId)
                    {
                        $mainBossId = $this->fixEnemyId($bossId);
                        $bosses[] =
                        [
                            'instance' => $id,
                            'npc_enemy' => $bossId,
                            'patch' => $this->patch,
                        ];

                        if (count($bosses) == 500)
                        {
                            $this->insert('xiv_instances_to_bosses', $bosses);
                            $bosses = [];
                        }
                    }

                    // add items
                    if (isset($boss['Treasure']))
                    {
                        foreach($boss['Treasure'] as $chestNum => $treasure)
                        {
                            $week_restriction = $treasure['WeekRestrictionIndex'];

                            foreach($treasure['Item'] as $itemId)
                            {
                                $items[] =
                                [
                                    'instance' => $id,
                                    'item' => $itemId,
                                    'week_restriction' => $week_restriction,
                                    'patch' => $this->patch,
                                ];

                                if (count($items) == 500)
                                {
                                    $this->insert('xiv_instances_to_items', $items);
                                    $items = [];
                                }

                                $chests[] =
                                [
                                    'instance' => $id,
                                    'chest_num' => $chestNum,
                                    'item' => $itemId,
                                    'week_restriction' => $week_restriction,
                                    'info' => 'main',
                                    'boss' => $mainBossId,
                                    'x' => 0, 'y' => 0,
                                    'patch' => $this->patch,
                                ];

                                if (count($chests) == 500)
                                {
                                    $this->insert('xiv_instances_chests_to_items', $chests);
                                    $chests = [];
                                }
                            }
                        }
                    }

                    // add currency A
                    if ($boss['ClearA'] > 0)
                    {
                        $currency[] =
                        [
                            'instance' => $id,
                            'value' => $boss['ClearA'],
                            'letter' => 'A',
                            'item' => $this->tomestones['A'],
                            'boss' => $mainBossId,
                            'patch' => $this->patch
                        ];
                    }

                    // add currency B
                    if ($boss['ClearB'] > 0)
                    {
                        $currency[] =
                        [
                            'instance' => $id,
                            'value' => $boss['ClearB'],
                            'letter' => 'B',
                            'item' => $this->tomestones['B'],
                            'boss' => $mainBossId,
                            'patch' => $this->patch
                        ];
                    }

                    // subnpcs $enemies
                    if (isset($boss['SubBNpcNames']))
                    {
                        foreach($boss['SubBNpcNames'] as $enemyId)
                        {
                            $enemyId = $this->fixEnemyId($enemyId);
                            $enemies[] =
                            [
                                'instance' => $id,
                                'enemy' => $enemyId,
                                'patch' => $this->patch
                            ];
                        }
                    }

                    // has reward
                    if (isset($boss['RewardItems']))
                    {
                        foreach($boss['RewardItems'] as $itemdata)
                        {
                            $rewards[] =
                            [
                                'instance' => $id,
                                'item' => $itemdata['Item'],
                                'quest' => isset($itemdata['Quest']) ? $itemdata['Quest'] : null,
                                'has_rate_condition' => $itemdata['has_rate_condition'],
                                'patch' => $this->patch,
                            ];
                        }
                    }
                }

                // middle boss
                if (isset($json['MiddleBoss']))
                {
                    foreach($json['MiddleBoss'] as $middleboss)
                    {
                        $mainBossId = null;

                        // add bosses
                        foreach($middleboss['BNpcNames'] as $bossId)
                        {
                            $mainBossId = $this->fixEnemyId($bossId);

                            $bosses[] =
                            [
                                'instance' => $id,
                                'npc_enemy' => $bossId,
                                'patch' => $this->patch,
                            ];

                            if (count($bosses) == 500)
                            {
                                $this->insert('xiv_instances_to_bosses', $bosses);
                                $bosses = [];
                            }
                        }

                        // add items
                        if (isset($middleboss['Treasure']))
                        {
                            foreach($middleboss['Treasure'] as $chestNum => $treasure)
                            {
                                $week_restriction = $treasure['WeekRestrictionIndex'];
                                foreach($treasure['Item'] as $itemId)
                                {
                                    $items[] =
                                    [
                                        'instance' => $id,
                                        'item' => $itemId,
                                        'week_restriction' => $week_restriction,
                                        'patch' => $this->patch,
                                    ];

                                    if (count($items) == 500)
                                    {
                                        $this->insert('xiv_instances_to_items', $items);
                                        $items = [];
                                    }

                                    $chests[] =
                                    [
                                        'instance' => $id,
                                        'chest_num' => $chestNum,
                                        'item' => $itemId,
                                        'week_restriction' => $week_restriction,
                                        'info' => 'mid',
                                        'boss' => $mainBossId,
                                        'x' => 0, 'y' => 0,
                                        'patch' => $this->patch,
                                    ];

                                    if (count($chests) == 500)
                                    {
                                        $this->insert('xiv_instances_chests_to_items', $chests);
                                        $chests = [];
                                    }
                                }
                            }
                        }

                        // add currency A
                        if ($middleboss['ClearA'] > 0)
                        {
                            $currency[] =
                            [
                                'instance' => $id,
                                'value' => $middleboss['ClearA'],
                                'letter' => 'A',
                                'item' => $this->tomestones['A'],
                                'boss' => $mainBossId,
                                'patch' => $this->patch
                            ];
                        }

                        // add currency B
                        if ($middleboss['ClearB'] > 0)
                        {
                            $currency[] =
                            [
                                'instance' => $id,
                                'value' => $middleboss['ClearB'],
                                'letter' => 'B',
                                'item' => $this->tomestones['B'],
                                'boss' => $mainBossId,
                                'patch' => $this->patch
                            ];
                        }

                        // subnpcs $enemies
                        if (isset($middleboss['SubBNpcNames']))
                        {
                            foreach($middleboss['SubBNpcNames'] as $enemyId)
                            {
                                $enemies[] =
                                [
                                    'instance' => $id,
                                    'enemy' => $enemyId,
                                    'patch' => $this->patch
                                ];
                            }
                        }
                    }
                }
            }
        }

        $this->insert($table, $insert);
        $this->insert('xiv_instances_chests_to_items', $chests);
        $this->insert('xiv_instances_to_bosses', $bosses);
        $this->insert('xiv_instances_to_items', $items);
        $this->insert('xiv_instances_to_currency', $currency);
        $this->insert('xiv_instances_to_enemy', $enemies);
        $this->insert('xiv_instances_to_rewards', $rewards);

        return true;
    }
}
