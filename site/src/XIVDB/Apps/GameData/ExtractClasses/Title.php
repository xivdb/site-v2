<?php

/**
 * Title
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Title extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_titles';

    protected $real =
    [
        3 => 'is_prefix',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'name_female_ja' => $line['name_female']['ja'],
            'name_female_en' => ucwords($line['name_female']['en']),
            'name_female_fr' => ucwords($line['name_female']['fr']),
            'name_female_de' => ucwords($line['name_female']['de']),
        ];
    }
}
