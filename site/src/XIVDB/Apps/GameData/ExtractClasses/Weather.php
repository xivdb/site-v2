<?php

/**
 * Weather
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Weather extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_weather';

    protected $real =
    [
        1 => 'icon',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'type_ja' => $line['type']['ja'],
            'type_en' => ucwords($line['type']['en']),
            'type_fr' => ucwords($line['type']['fr']),
            'type_de' => ucwords($line['type']['de']),
        ];
    }
}
