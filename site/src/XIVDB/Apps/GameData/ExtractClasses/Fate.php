<?php

/**
 * Fate
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Fate extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_fates';

    protected $real =
    [
        4 => 'class_level',
        5 => 'class_level_max',
        6 => 'event_item',
        11 => 'icon_small',
        12 => 'icon',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),
        ];
    }
}
