<?php

/**
 * MonsterNoteTarget
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class MonsterNoteTarget extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_hunting_log_targets';

    protected $real =
    [
        1 => 'enemy',
        2 => 'icon',

        4 => 'placename_placename_1',
        5 => 'placename_zone_1',

        6 => 'placename_placename_2',
        7 => 'placename_zone_2',

        8 => 'placename_placename_3',
        9 => 'placename_zone_3',
    ];
}
