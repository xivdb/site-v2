<?php

/**
 * CraftLeve
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CraftLeve extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_craft_leve';

    protected $real =
    [
        1 => 'leve',
        3 => 'repeats',
    ];

    protected $sets =
    [
        4 => 'item_1',
        5 => 'item_quantity_1',
        6 => 'item_hq_1',

        7 => 'item_2',
        8 => 'item_quantity_2',
        9 => 'item_hq_2',

        10 => 'item_3',
        11 => 'item_quantity_3',
        12 => 'item_hq_3',

        13 => 'item_4',
        14 => 'item_quantity_4',
        15 => 'item_hq_4',
    ];
}
