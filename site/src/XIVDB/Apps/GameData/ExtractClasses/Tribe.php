<?php

/**
 * Tribe
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Tribe extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_tribes';

    protected $real =
    [
        5 => 'str',
        6 => 'dex',
        7 => 'vit',
        8 => 'int',
        9 => 'mnd',
        10 => 'pie',
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
