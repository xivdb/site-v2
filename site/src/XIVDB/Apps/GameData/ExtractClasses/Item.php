<?php

/**
 * Items
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Item extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_items';

    protected $real =
    [
        5 => 'starts_with_vowel',
        11 => 'icon',
        12 => 'level_item',
        13 => 'rarity',

        15 => 'stain',
        16 => 'item_ui_category',
        17 => 'item_search_category',
        18 => 'equip_slot_category',

        20 => 'stack_size',
        21 => 'is_unique',
        22 => 'is_untradable',
        23 => 'is_indisposable',
        24 => 'is_crest_worthy',
        //25 => 'price_mid',
        26 => 'price_sell',
        27 => 'can_be_hq',
        28 => 'is_dyeable',
        30 => 'item_action',

        32 => 'cooldown_seconds',
        33 => 'classjob_repair',
        34 => 'item_repair',
        35 => 'item_glamour',
        36 => 'desynthesize',
        //36 => 'is_collectable',
        39 => 'level_equip',

        //42 => 'equippable_by_race_hyur',
        //43 => 'equippable_by_race_elezen',
        //44 => 'equippable_by_race_lalafell',
        //45 => 'equippable_by_race_miqote',
        //46 => 'equippable_by_race_roegadyn',
        //47 => 'equippable_by_race_aura',
        //48 => 'equippable_by_gender_m',
        //49 => 'equippable_by_gender_f',

        42 => 'classjob_category',
        43 => 'grand_company',
        44 => 'item_series',
        45 => 'base_param_modifier',
        46 => 'model_main',
        47 => 'model_sub',
        48 => 'classjob_use',

        70 => 'item_special_bonus',

        84 => 'materialize_type',
        85 => 'materia_slot_count',
        // 94 => 'is_advanced_meld_permitted',
        87 => 'is_pvp',
    ];

    protected $baseStatsSaint =
    [
        50 => 'damage',
        51 => 'magic_damage',
        52 => 'delay',

        54 => 'block_rate',
        55 => 'block_strength',

        56 => 'defense_phys',
        57 => 'defense_mag',

        73 => 'primary_hq_modifier', // damage, block_rate, defense_phys
        75 => 'secondary_hq_modifier', // magic_damage, block_strength, defense_mag
    ];

    protected $paramStatsSaint =
    [
        [
            58 => 'param',
            59 => 'value',
            77 => 'hq',

        ],
        [
            60 => 'param',
            61 => 'value',
            79 => 'hq',
        ],
        [
            62 => 'param',
            63 => 'value',
            81 => 'hq',
        ],
        [
            64 => 'param',
            65 => 'value',
            83 => 'hq',
        ],
        [
            66 => 'param',
            67 => 'value',
            85 => 'hq',
        ],
        [
            68 => 'param',
            69 => 'value',
            87 => 'hq',
        ],
    ];

    /*protected $chinese = [
        0 => 'id',
        1 => 'name_cns',
        9 => 'help_cns',
        10 => 'plural_cns',
    ];
    */

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => $line['name']['en'],
            'name_fr' => $line['name']['fr'],
            'name_de' => $line['name']['de'],

            'help_ja' => $line['help']['ja'],
            'help_en' => $line['help']['en'],
            'help_fr' => $line['help']['fr'],
            'help_de' => $line['help']['de'],
        ];
    }

    // ----------------------------------
    // Manual scripts
    // ----------------------------------

    protected function manual()
    {
        $this->baseStats();
        $this->paramStats();
        $this->paramSpecialStats();
        $this->classjobs();
        $this->fixcategories();
    }

    /**
     * Base stats
     */
    private function baseStats()
    {
        $csv = $this->getCsvFileData();
        $offsets = array_flip($this->baseStatsSaint);

        $insert = [];
        foreach($csv as $id => $line) {
            $base = [
                'id'                    => $id,

                // base stats
                'damage'                => $line[$offsets['damage']],
                'damage_hq'             => $line[$offsets['damage']] + $line[$offsets['primary_hq_modifier']],
                'magic_damage'          => $line[$offsets['magic_damage']],
                'magic_damage_hq'       => $line[$offsets['magic_damage']] + $line[$offsets['secondary_hq_modifier']],
                'block_strength'        => $line[$offsets['block_strength']],
                'block_strength_hq'     => $line[$offsets['block_strength']] + $line[$offsets['secondary_hq_modifier']],
                'block_rate'            => $line[$offsets['block_rate']],
                'block_rate_hq'         => $line[$offsets['block_rate']] + $line[$offsets['primary_hq_modifier']],
                'defense'               => $line[$offsets['defense_phys']],
                'defense_hq'            => ($line[$offsets['defense_phys']] + $line[$offsets['primary_hq_modifier']]),
                'magic_defense'         => $line[$offsets['defense_mag']],
                'magic_defense_hq'      => ($line[$offsets['defense_mag']] + $line[$offsets['secondary_hq_modifier']]),
            ];


            // reset some to zero so that it doesnt mess up views or filters
            $base['damage_hq'] = $base['damage'] == 0 ? 0 : $base['damage_hq'];
            $base['magic_damage_hq'] = $base['magic_damage'] == 0 ? 0 : $base['magic_damage_hq'];
            $base['block_strength_hq'] = $base['block_strength'] == 0 ? 0 : $base['block_strength_hq'];
            $base['block_rate_hq'] = $base['block_rate'] == 0 ? 0 : $base['block_rate_hq'];
            $base['defense_hq'] = $base['defense'] == 0 ? 0 : $base['defense_hq'];
            $base['magic_defense_hq'] = $base['magic_defense'] == 0 ? 0 : $base['magic_defense_hq'];

            // attack speed = (attack speed / 1000) - 2 dp
            $base['delay'] = ($line[$offsets['delay']] > 0) ? $line[$offsets['delay']] / 1000 : 0;
            $base['delay_hq'] = ($line[$offsets['delay']] > 0) ? $line[$offsets['delay']] / 1000 : 0;

            // auto attack = (damage / 3 * attack speed) - 2 dp
            $base['auto_attack'] = ($line[$offsets['damage']] > 0) ? ($line[$offsets['damage']] / 3 * $line[$offsets['delay']]) / 1000 : 0;
            $base['auto_attack_hq'] = ($base['damage_hq'] > 0) ? ($base['damage_hq'] / 3 * $line[$offsets['delay']]) / 1000 : 0;

            // dps = (damage / 3) - 2dp
            $base['dps'] = ($line[$offsets['damage']] > 0) ? $line[$offsets['damage']] / 3 : 0;
            $base['dps_hq'] = ($base['damage_hq'] > 0) ? $base['damage_hq'] / 3 : 0;

            // round down
            $base['delay'] = $this->roundDown($base['delay'], 2);
            $base['delay_hq'] = $this->roundDown($base['delay_hq'], 2);
            $base['auto_attack'] = $this->roundDown($base['auto_attack'], 2);
            $base['auto_attack_hq'] = $this->roundDown($base['auto_attack_hq'], 2);
            $base['dps'] = $this->roundDown($base['dps'], 2);
            $base['dps_hq'] = $this->roundDown($base['dps_hq'], 2);

            // if set bonus, remove hq values
            $specialOffset = array_flip($this->real)['item_special_bonus'];
            $specialStats = intval($line[$specialOffset]);
            if (in_array($specialStats, [2,4,6])) {
                $base['damage_hq'] = 0;
                $base['magic_damage_hq'] = 0;
                $base['block_strength_hq'] = 0;
                $base['block_rate_hq'] = 0;
                $base['defense_hq'] = 0;
                $base['magic_defense_hq'] = 0;
                $base['delay_hq'] = 0;
                $base['auto_attack_hq'] = 0;
                $base['dps_hq'] = 0;
                $base['delay_hq'] = 0;
                $base['auto_attack_hq'] = 0;
            }

            $base['patch'] = $this->patch;

            $insert[$id] = $base;
        }

        $this->insert('xiv_items_base_stats', $insert);
    }

    /**
     * Param stats
     */
    private function paramStats()
    {
        $csv = $this->getCsvFileData();
        $offsets = $this->paramStatsSaint;

        $insert = [];
        foreach($csv as $id => $line) {
            // go through each param
            foreach($offsets as $p) {
                $p = array_flip($p);

                $i_value = $p['value'];
                $i_param = $p['param'];

                $value = $line[$i_value];
                $param = $line[$i_param];

                // if hq
                if (isset($p['hq'])) {
                    $i_hq = $p['hq'];
                    $hq = ($i_hq != null && $line[$i_hq] != 0) ? ($line[$i_value] + $line[$i_hq]) : 0;
                }

                // check values
                if ($id == 0 || $value == 0 || $param == 0) {
                    continue;
                }

                $insert[] = [
                    'item' => $id,
                    'baseparam' => $param,
                    'value' => $value,
                    'value_hq' => $hq,
                    'special' => 0,
                    'patch' => $this->patch,
                ];
            }
        }

        $this->insert('xiv_items_to_baseparam', $insert);
    }

    /**
     * Special Param stats
     */
    private function paramSpecialStats()
    {
        $offsets = [
            [ 'param' => 80, 'value' => 81 ],
            [ 'param' => 82, 'value' => 83 ],
            [ 'param' => 84, 'value' => 85 ],
            [ 'param' => 86, 'value' => 87 ],
            [ 'param' => 88, 'value' => 89 ],
            [ 'param' => 90, 'value' => 91 ],
        ];

        $csv = $this->getCsvFileData();
        $specialOffset = array_flip($this->real)['item_special_bonus'];

        $insert = [];
        foreach($csv as $id => $line)
        {
            // if set bonus, remove hq values
            $specialStats = intval($line[$specialOffset]);

            // only these have values, 2 = Set Bonus, 4 = Sanction, 6 Set Bonus (capped)
            if (!in_array($specialOffset, [2,4,6])) {
                continue;
            }

            // go through each param
            foreach($offsets as $p)
            {
                $i_value = $p['value'];
                $i_param = $p['param'];

                $value = $line[$i_value];
                $param = $line[$i_param];

                // check values
                if ($id == 0 || $value == 0 || $param == 0) {
                    continue;
                }

                $insert[] = [
                    'item' => $id,
                    'baseparam' => $param,
                    'value' => $value,
                    'special' => 1,
                    'patch' => $this->patch,
                ];
            }
        }

        $this->insert('xiv_items_to_baseparam', $insert);
    }

    /**
     * Class jobs, sorts the item to class job table
     */
    private function classjobs()
    {
        $csv = $this->getCsvFileData();

        $classjobToCategory =  $this->xivClassJobsToCategory();
        $classjobCategoryOffset = array_flip($this->real)['classjob_category'];

        $insert = [];
        foreach($csv as $id => $line)
        {
            // get class job category
            $cat = trim($line[$classjobCategoryOffset]);

            // skip 0, they likely items or somethings
            if ($cat == 0) {
                continue;
            }

            // ensure category exists, otherwise some issue
            if (!isset($classjobToCategory[$cat])) {
                die('classjobs_to_category does not contain id: '. $cat .' - ensure class jobs and class jobs category extracts have run');
            }

            // set class list
            $data = $classjobToCategory[$cat];

            foreach($data as $cjid)
            {
                $insert[] =
                [
                    'item' => $id,
                    'classjob' => $cjid,
                    'patch' => $this->patch,
                ];
            }
        }

        $this->insert('xiv_items_to_classjob', $insert);
    }

    /**
     * fixes slot_equip and kind on items
     */
    private function fixcategories()
    {
        $categoryIdToSlotId =
        [
            // main hand
            1 => [
                1,2,3,4,5,6,7,8,9,10,84,87,88,89,96,97,98,
                12,14,16,18,20,22,24,26,28,30,32,
            ],

            // off hand
            2 => [
                13,15,17,19,21,23,25,27,29,31,11
            ],

            // head, body, hands, waist, legs, feet, ears, neck, wrists, ring
            3 => [34],
            4 => [35],
            5 => [37],
            6 => [39],
            7 => [36],
            8 => [38],
            9 => [41],
            10 => [40],
            11 => [42],
            12 => [43],
            13 => [62],
            14 => [46,47],
            15 => [44],
            16 => [58],
        ];

        $csv = $this->getCsvFileData();

        $itemUiCategoryList = $this->xivItemsUiCategory();
        $itemUiCategoryOffset = array_flip($this->real)['item_ui_category'];

        // insert
        $insert = [];
        foreach($csv as $id => $line)
        {
            // continue if name is empty
            if (empty($line[1])) {
                continue;
            }

            $itemUiCategory = trim($line[$itemUiCategoryOffset]);

            $add = [ 'id' => $id ];
            $add['slot_equip'] = null;

            // find real slot id
            foreach($categoryIdToSlotId as $slotEquipId => $categorylist)
            {
                // loop through slot ids
                if (in_array($itemUiCategory, $categorylist))
                {
                    $add['slot_equip'] = $slotEquipId;
                    break;
                }
            }

            // append on kind
            $add['item_ui_kind'] = ($itemUiCategory != 0) ? $itemUiCategoryList[$itemUiCategory]['item_ui_kind'] : 0;
            $add['patch'] = $this->patch;

            $insert[] = $add;
        }

        $this->insert('xiv_items', $insert);
    }
}
