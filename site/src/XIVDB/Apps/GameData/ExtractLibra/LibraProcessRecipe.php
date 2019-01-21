<?php

/**
 * LibraProcessRecipe
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessRecipe extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_recipes';

        $insert = [];
        $items = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new = [

                /*'can_quick_synth'        => $d['CanAutoCraft'],
                'can_hq'                => $d['CanHq'],
                'craft_quantity'        => $d['CraftNum'],
                'craft_type'            => $d['CraftType'],
                'level'                 => $d['Level'],
                'level_view'            => $d['levelView'],
                'level_diff'            => $d['levelDiff'],
                'recipe_element'        => $d['Element'],
                'item'                  => $d['CraftItemId'],
                'required_craftsmanship'     => $d['NeedCraftmanship'],
                'required_control'          => $d['NeedControl'],
                'status_required'           => $d['NeedStatus'],
                'item_required'             => $d['NeedEquipItem'],
                'material_point'        => isset($json['material_point']) ? $json['material_point'] : 0,
                'quality'           => isset($json['quality']) ? $json['quality'] : 0,
                'work_max'              => isset($json['work_max']) ? $json['work_max'] : 0,
                'number'                => $d['Number'],*/

                'lodestone_type'        => $lodestone[0],
                'lodestone_id'          => $lodestone[1],
            ];

            $new = array_merge($new, $this->names($d, 'Index'), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }

            // recipe tree
            if (isset($json['Crystal']))
            {
                foreach($json['Crystal'] as $itemdata)
                {
                    foreach($itemdata as $itemId => $quantity)
                    {
                        $items[] =
                        [
                            'recipe' => $id,
                            'item' => $itemId,
                            'quantity' => $quantity,
                            'patch' => $this->patch
                        ];

                        if (count($items) == 500)
                        {
                            $this->insert('xiv_recipes_to_item', $items);
                            $items = [];
                        }
                    }
                }
            }

            if (isset($json['Item']))
            {
                foreach($json['Item'] as $itemdata)
                {
                    foreach($itemdata as $itemId => $quantity)
                    {
                        $items[] =
                        [
                            'recipe' => $id,
                            'item' => $itemId,
                            'quantity' => $quantity,
                            'patch' => $this->patch
                        ];

                        if (count($items) == 500)
                        {
                            $this->insert('xiv_recipes_to_item', $items);
                            $items = [];
                        }
                    }
                }
            }
        }

        $this->insert($table, $insert);
        $this->insert('xiv_recipes_to_item', $items);

        return true;
    }
}
