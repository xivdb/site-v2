<?php

/**
 * FccShop
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class FccShop extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_fcc_shop';

    protected $set =
    [
        2 => 'item_1',
        3 => 'item_2',
        4 => 'item_3',
        5 => 'item_4',
        6 => 'item_5',
        7 => 'item_6',
        8 => 'item_7',
        9 => 'item_8',
        10 => 'item_9',
        11 => 'item_10',

        12 => 'cost_1',
        13 => 'cost_2',
        14 => 'cost_3',
        15 => 'cost_4',
        16 => 'cost_5',
        17 => 'cost_6',
        18 => 'cost_7',
        19 => 'cost_8',
        20 => 'cost_9',
        21 => 'cost_10',

        22 => 'rank_1',
        23 => 'rank_2',
        24 => 'rank_3',
        25 => 'rank_4',
        26 => 'rank_5',
        27 => 'rank_6',
        28 => 'rank_7',
        29 => 'rank_8',
        30 => 'rank_9',
        31 => 'rank_10',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),
        ];
    }
}
