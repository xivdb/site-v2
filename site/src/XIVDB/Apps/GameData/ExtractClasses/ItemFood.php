<?php

/**
 * ItemFood
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ItemFood extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = false;

    protected $itemFood =
    [
        [
            2 => 'param',
            3 => 'is_relative',
            4 => 'value',
            5 => 'value_max',
            6 => 'value_hq',
            7 => 'value_max_hq',
        ],

        [
            8 => 'param',
            9 => 'is_relative',
            10 => 'value',
            11 => 'value_max',
            12 => 'value_hq',
            13 => 'value_max_hq',
        ],

        [
            14 => 'param',
            15 => 'is_relative',
            16 => 'value',
            17 => 'value_max',
            18 => 'value_hq',
            19 => 'value_max_hq',
        ]
    ];

    protected $itemAction =
    [
        5 => 'type',
        7 => 'item_food',
        8 => 'duration',
    ];

    protected function manual()
    {
        $this->foodStats();
    }

    private function foodStats()
    {
        $insert = [];
        $insert2 = [];

        $itemActionIdOffset = (new Item())->getOffset('item_action');
        $itemActionOffsets = array_flip($this->itemAction);

        $itemCSV = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'Item.csv');
        $itemActionCSV = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'ItemAction.csv');
        $itemFoodCSV = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'ItemFood.csv');

        //
        // Go through all items
        //
        foreach($itemCSV as $id => $item)
        {
            // get ID of item action
            $itemActionId = $item[$itemActionIdOffset];

            // if no item action, continue
            if (!$itemActionId || $itemActionId == 0) continue;

            // get item Action data
            $itemActionData = $itemActionCSV[$itemActionId];

            // if no item action data, continue
            if (!$itemActionData) continue;

            $itemFoodId = $itemActionData[$itemActionOffsets['item_food']];
            $itemFoodDuration = $itemActionData[$itemActionOffsets['duration']];

            // get food
            $itemFoodData = isset($itemFoodCSV[$itemFoodId]) ? $itemFoodCSV[$itemFoodId] : null;

            // if no item food data, continue
            if (!$itemFoodData) continue;

            foreach($this->itemFood as $data)
            {
                $data = array_flip($data);

                $insert[] =
                [
                    'item' => $id,
                    'baseparam' => $itemFoodData[$data['param']],
                    'is_relative' => $itemFoodData[$data['param']],
                    'is_food' => 1,
                    'percent' => $itemFoodData[$data['value']],
                    'percent_hq' => $itemFoodData[$data['value_hq']],
                    'value' => $itemFoodData[$data['value_max']],
                    'value_hq' => $itemFoodData[$data['value_max_hq']],
                ];

                $insert2[$id] =
                [
                    'id' => $id,
                    'item_duration' => $itemFoodDuration,
                ];
            }
        }

        $this->insert('xiv_items_to_baseparam', $insert);
        $this->insert('xiv_items', $insert2);
    }
}
