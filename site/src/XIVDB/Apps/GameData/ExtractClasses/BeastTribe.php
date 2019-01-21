<?php

/**
 * BeastTribe
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class BeastTribe extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_beast_tribe';

    protected $real =
    [
        2 => 'icon_reputation',
        3 => 'icon',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'reputation_value_ja' => $line['reputation']['ja'],
            'reputation_value_en' => ($line['reputation']['en']),
            'reputation_value_fr' => ($line['reputation']['fr']),
            'reputation_value_de' => ($line['reputation']['de']),
        ];
    }
}
