<?php

/**
 * RelicNote
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class RelicNote extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_relic_notes';

    protected $real =
    [
        1 => 'event_item',

        22 => 'nm_target_1',
        23 => 'nm_target_2',
        24 => 'nm_target_3',

        26 => 'fate_1',
        27 => 'fate_placename_1',
        28 => 'fate_2',
        29 => 'fate_placename_2',
        30 => 'fate_3',
        31 => 'fate_placename_3',

        32 => 'leve_1',
        33 => 'leve_2',
        34 => 'leve_3',
    ];

    protected $repeater =
    [
        'start' => 2,
        'finish' => 21,

        'columns' => [
            'monster_note_target',
            'monster_count'
        ],
    ];
}
