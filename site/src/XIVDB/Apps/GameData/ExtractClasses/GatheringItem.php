<?php

/**
 * GatheringItem
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringItem extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_gathering_item';

    protected $real =
    [
        1 => 'item',
        2 => 'level',
        3 => 'is_hidden',
    ];
}
