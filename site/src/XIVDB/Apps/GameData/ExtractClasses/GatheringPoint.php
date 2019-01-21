<?php

/**
 * GatheringPoint
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringPoint extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_gathering_point';

    protected $real =
    [
        2 => 'gathering_point_base',
        4 => 'gathering_point_bonus_1',
        5 => 'gathering_point_bonus_2',
        6 => 'territory_id',
        7 => 'placename',
        8 => 'gathering_sub_category',
    ];
}
