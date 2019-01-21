<?php

/**
 * LibraProcessENpcResident
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessENpcResident extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_npc';

        $insert = [];
        $quest = [];
        $area = [];
        $shop = [];
        $shopids = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new =
            [
                'lodestone_type' => $lodestone[0].'/'.$lodestone[1],
                'lodestone_id' => $lodestone[2],

                'has_shop' => $d['has_shop'],
                'has_shop_conditional' => $d['has_condition_shop'],
                'has_quest' => $d['has_quest']
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

            if (isset($json['client_quest']))
            {
                foreach($json['client_quest'] as $value)
                {
                    $quest[] = [
                        'npc' => $id,
                        'quest' => $value,
                        'is_client' => 1,
                        'patch' => $this->patch
                    ];

                    if (count($quest) == 500)
                    {
                        $this->insert('xiv_npc_to_quest', $quest);
                        $quest = [];
                    }
                }
            }

            if (isset($json['quest']))
            {
                foreach($json['quest'] as $value)
                {
                    $quest[] = [
                        'npc' => $id,
                        'quest' => $value,
                        'is_client' => 0,
                        'patch' => $this->patch
                    ];

                    if (count($quest) == 500)
                    {
                        $this->insert('xiv_npc_to_quest', $quest);
                        $quest = [];
                    }
                }
            }

            if (isset($json['coordinate']))
            {
                foreach($json['coordinate'] as $placenameId => $areas)
                {
                    foreach($areas as $coordinates)
                    {
                        $area[] = [
                            'npc' => $id,
                            'placename' => $placenameId,
                            'x' => $coordinates[0],
                            'y' => $coordinates[1],
                            'patch' => $this->patch
                        ];

                        if (count($area) == 500)
                        {
                            $this->insert('xiv_npc_to_area', $area);
                            $area = [];
                        }
                    }
                }
            }

            if (isset($json['shop']))
            {
                foreach($json['shop'] as $shoplist)
                {
                    foreach($shoplist as $shopId => $itemList)
                    {
                        if (!isset($shopids[$shopId]))
                        {
                            $shopids[$shopId] = [
                                'npc' => $id,
                                'shop' => $shopId,
                                'patch' => $this->patch
                            ];

                            if (count($shopids) == 500)
                            {
                                $this->insert('xiv_npc_to_shop', $shopids);
                                $shopids = [];
                            }
                        }

                        foreach($itemList as $shopitem)
                        {
                            foreach($shopitem as $itemId => $shopdata)
                            {
                                $shop[] = [
                                    'shop' => $shopId,
                                    'item' => $itemId,
                                    'data' => json_encode($shopdata),
                                    'patch' => $this->patch
                                ];

                                if (count($shop) == 500)
                                {
                                    $this->insert('xiv_shops_to_item', $shop);
                                    $shop = [];
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->insert($table, $insert);
        $this->insert('xiv_npc_to_quest', $quest);
        $this->insert('xiv_npc_to_area', $area);
        $this->insert('xiv_npc_to_shop', $shopids);
        $this->insert('xiv_shops_to_item', $shop);

        return true;
    }
}
