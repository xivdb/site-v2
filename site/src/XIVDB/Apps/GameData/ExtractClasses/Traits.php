<?php

/**
 * Trait
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Traits extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_traits';

    protected $real =
    [
        2 => 'icon',
        3 => 'classjob',
        4 => 'level',
        6 => 'value',
        7 => 'classjob_category',
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
