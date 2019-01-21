<?php

/**
 * EventItem
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class EventItem extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_event_items';

    protected $real =
    [
        6 => 'rarity',
        11 => 'icon',
        13 => 'stack_size',
        14 => 'quest',
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
