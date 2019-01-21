<?php

/**
 * Mount
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Mount extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_mounts';

    protected $unknown =
    [
        15 => 'race_scale',
        16 => 'race_scale',
        17 => 'race_scale',
        18 => 'race_scale',
        19 => 'race_scale',
        20 => 'race_scale',
        21 => 'race_scale',
        22 => 'race_scale',
        23 => 'race_scale',
        24 => 'race_scale',
        25 => 'race_scale',
        26 => 'race_scale',
    ];

    protected $real =
    [
        11 => 'can_fly_extra',
        15 => 'can_fly',
        31 => 'icon',
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
            'name_plural_en' => ucwords($line['name_plural']['en']),
            'name_plural_fr' => ucwords($line['name_plural']['fr']),
            'name_plural_de' => ucwords($line['name_plural']['de']),
        ];
    }
}
