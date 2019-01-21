<?php

/**
 * LibraProcessBNpcName
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessBNpcName extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_npc_enemy';

        $insert = [];
        $nonpop = [];
        $item = [];
        $instance = [];
        $region = [];
        foreach($this->data as $i => $d)
        {
            $id = $this->fixEnemyId($d['Key']);
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new =
            [
                'lodestone_type' => $lodestone[0].'/'.$lodestone[1],
                'lodestone_id' => $lodestone[2],
            ];

            $new = array_merge($new, $this->names($d, 'SGL'), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }

            // - - - - - - - - - - - - - - - - - - - -
            // Data
            // - - - - - - - - - - - - - - - - - - - -

            if (isset($json['nonpop']))
            {
                foreach($json['nonpop'] as $value)
                {
                    $nonpop[] = [
                        'enemy' => $id,
                        'placename' => $value,
                        'patch' => $this->patch
                    ];

                    if (count($nonpop) == 500)
                    {
                        $this->insert('xiv_npc_enemy_nonpop', $nonpop);
                        $nonpop = [];
                    }
                }
            }

            if (isset($json['item']))
            {
                foreach($json['item'] as $value)
                {
                    $item[] = [
                        'enemy' => $id,
                        'item' => $value,
                        'patch' => $this->patch
                    ];

                    if (count($item) == 500)
                    {
                        $this->insert('xiv_npc_enemy_to_items', $item);
                        $item = [];
                    }
                }
            }

            if (isset($json['instance_contents']))
            {
                foreach($json['instance_contents'] as $value)
                {
                    $instance[] = [
                        'enemy' => $id,
                        'instance' => $value,
                        'patch' => $this->patch
                    ];
                }
            }

            if (isset($json['region']))
            {
                foreach($json['region'] as $regionId => $placenames)
                {
                    foreach($placenames as $placenameId => $levels)
                    {
                        foreach($levels as $level)
                        {
                            $region[] = [
                                'enemy' => $id,
                                'region' => $regionId,
                                'placename' => $placenameId,
                                'levels' => $level,
                                'patch' => $this->patch,
                            ];

                            if (count($region) == 500)
                            {
                                $this->insert('xiv_npc_enemy_to_area', $region);
                                $region = [];
                            }
                        }
                    }
                }
            }
        }

        $this->insert($table, $insert);
        $this->insert('xiv_npc_enemy_nonpop', $nonpop);
        $this->insert('xiv_npc_enemy_to_items', $item);
        $this->insert('xiv_npc_enemy_to_instance', $instance);
        $this->insert('xiv_npc_enemy_to_area', $region);

        return true;
    }
}
