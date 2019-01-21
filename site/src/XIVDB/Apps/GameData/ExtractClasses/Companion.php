<?php

/**
 * Companion
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Companion extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_companions';

    protected $real =
    [
        15 => 'behavior',
        27 => 'icon',
        31 => 'cost',
        32 => 'hp',
        35 => 'skill_cost',
        38 => 'minion_race',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'name_plural_ja' => $line['name_plural']['ja'],
            'name_plural_en' => ($line['name_plural']['en']),
            'name_plural_fr' => ($line['name_plural']['fr']),
            'name_plural_de' => ($line['name_plural']['de']),
        ];
    }
}
