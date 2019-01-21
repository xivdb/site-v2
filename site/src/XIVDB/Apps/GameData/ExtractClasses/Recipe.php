<?php

/**
 * Recipe
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Recipe extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_recipes';

    protected $group =
    [
        6 => 'mat_item_1',
        8 => 'mat_item_2',
        10 => 'mat_item_3',
        12 => 'mat_item_4',
        14 => 'mat_item_5',
        16 => 'mat_item_6',

        22 => 'crystal_1',
        24 => 'crystal_2',

        7 => 'mat_quantity_1',
        9 => 'mat_quantity_2',
        11 => 'mat_quantity_3',
        13 => 'mat_quantity_4',
        15 => 'mat_quantity_5',
        17 => 'mat_quantity_6',

        23 => 'crystal_quantity_1',
        25 => 'crystal_quantity_2',
    ];

    protected $real =
    [
        2 => 'craft_type',
        3 => 'level',
        4 => 'item',
        5 => 'craft_quantity',
        26 => 'recipe_element',
        27 => 'recipe_notebook',
        28 => 'is_secondary',
        29 => 'difficulty_factor',
        30 => 'quality_factor',
        31 => 'durability_factor',
        33 => 'required_craftsmanship',
        34 => 'required_control',
        35 => 'quick_synth_craftsmanship',
        36 => 'quick_synth_control',
        37 => 'unlock_key',
        38 => 'can_quick_synth',
        39 => 'can_hq',
        41 => 'status_required',
        42 => 'item_required',
        43 => 'is_specialization_required',
    ];

    protected function manual()
    {
        $this->classjob();
        $this->materials();
        $this->names();
        $this->values();
    }

    /**
     * Handle materials for the recipe
     */
    private function materials()
    {
        // get mapping and flip it, so we can use full names as indexes
        $mapped = array_flip($this->group);

        // offsets for items
        $offsets =
        [
            $mapped['mat_item_1'] => $mapped['mat_quantity_1'],
            $mapped['mat_item_2'] => $mapped['mat_quantity_2'],
            $mapped['mat_item_3'] => $mapped['mat_quantity_3'],
            $mapped['mat_item_4'] => $mapped['mat_quantity_4'],
            $mapped['mat_item_5'] => $mapped['mat_quantity_5'],
            $mapped['mat_item_6'] => $mapped['mat_quantity_6'],
        ];

        // offsets for crystals
        $crystals =
        [
            $mapped['crystal_1'] => $mapped['crystal_quantity_1'],
            $mapped['crystal_2'] => $mapped['crystal_quantity_2'],
        ];

        // build recipe to item
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            // loop through items
            foreach($offsets as $item => $quantity)
            {
                // skip if no quantity
                if ($line[$quantity] == '0') continue;

                $insert[] =
                [
                    'recipe' => $id,
                    'item'   => $line[$item],
                    'quantity'  => $line[$quantity],
                    'patch'     => $this->patch,
                ];
            }

            // loop through crystals
            foreach($crystals as $crystal => $quantity)
            {
                // skip if no quantity
                if ($line[$quantity] == '0') continue;

                $insert[] =
                [
                    'recipe' => $id,
                    'item'   => intval($line[$crystal]),
                    'quantity'  => $line[$quantity],
                    'patch'     => $this->patch,
                ];
            }
        }

        $this->insert('xiv_recipes_to_item', $insert);
    }

    /**
     * Handle the class job for the recipe
     */
    private function classjob()
    {
        $typeOffset = array_flip($this->real)['craft_type'];
        $typeArray =
        [
            0 => 8,
            1 => 9,
            2 => 10,
            3 => 11,
            4 => 12,
            5 => 13,
            6 => 14,
            7 => 15,
        ];

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $craftType = $line[$typeOffset];
            $classJobId = $typeArray[$craftType];

            $insert[] =
            [
                'id' => $id,
                'classjob' => $classJobId,
            ];
        }

        $this->insert('xiv_recipes', $insert);
    }

    /**
     * Sort recipe names
     */
    private function names()
    {
        $offset = array_flip($this->real)['item'];
        $itemNames = $this->xivItemNames();

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $itemId = $line[$offset];

            // make sure item id above 0
            if ($itemId > 0)
            {
                // get names
                $names = $itemNames[$itemId];
                $names['id'] = $id;

                // add to list
                $insert[] = $names;
            }
        }

        $this->insert('xiv_recipes', $insert);
    }

    /**
     * Sort values from RecipeLevelTable
     * Includes: material_point, level_view (Real level) and level_diff (number of stars)
     */
    private function values()
    {
        $insert = [];

        // get levels table
        $lvTables = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'RecipeLevelTable.csv');
        $levelOffset = array_flip($this->real)['level'];

        // go through recipes
        foreach($this->getCsvFileData() as $id => $line)
        {
            $level = $line[$levelOffset];
            $table = $lvTables[$level];

            $insert[] =
            [
                'id' => $id,
                'level_view' => $table[1],
                'level_diff' => $table[2],
                // ???
                'difficulty' => $table[4],
                'quality' => $table[5],
                'durability' => $table[6],
            ];
        }

        $this->insert('xiv_recipes', $insert);
    }
}
