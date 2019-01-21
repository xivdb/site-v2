<?php

/**
 * Level
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Level extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_level';

    protected $real =
    [
        0 => 'id',
        1 => 'x',
        2 => 'y',
        3 => 'z',
        4 => 'yaw',
        5 => 'radius',
        6 => 'type',
        7 => 'object_key',
        8 => 'map',
    ];
}
