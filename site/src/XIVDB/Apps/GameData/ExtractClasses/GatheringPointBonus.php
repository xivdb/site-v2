<?php

/**
 * GatheringPointBonus
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringPointBonus extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gathering_point_bonus';

    protected $real =
    [
        1 => 'condition',
        2 => 'condition_value',
        4 => 'bonus_type',
        5 => 'bonus_value',
    ];
}
