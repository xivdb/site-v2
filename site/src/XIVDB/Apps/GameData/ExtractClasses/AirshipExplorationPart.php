<?php

/**
 * AirshipExplorationPart
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class AirshipExplorationPart extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_airship_exploration_part';

    protected $real =
    [
        2 => 'rank',
        3 => 'components',
        4 => 'surveillance',
        5 => 'retrieval',
        6 => 'speed',
        7 => 'range',
        8 => 'favor',
        10 => 'repair_materials',
    ];
}
