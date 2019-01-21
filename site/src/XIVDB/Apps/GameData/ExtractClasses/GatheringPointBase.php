<?php

/**
 * GatheringPointBase
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringPointBase extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_gathering_point_base';

    protected $real =
    [
        1 => 'gathering_type',
        2 => 'level',
        12 => 'is_limited',
    ];

    protected $set =
    [
        4 => 'gathering_item_1',
        5 => 'gathering_item_2',
        6 => 'gathering_item_3',
        7 => 'gathering_item_4',
        8 => 'gathering_item_5',
        9 => 'gathering_item_6',
        10 => 'gathering_item_7',
        11 => 'gathering_item_8',
    ];
}
