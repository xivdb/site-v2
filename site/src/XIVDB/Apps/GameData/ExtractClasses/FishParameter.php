<?php

/**
 * FishParameter
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class FishParameter extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_fish_parameters';

    protected $real =
    [
        2 => 'item',
        3 => 'level',
        6 => 'is_fish',
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
