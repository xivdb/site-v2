<?php

/**
 * Race
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Race extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_races';

    protected $real =
    [
        3 => 'rse_m_body',
        4 => 'rse_m_hands',
        5 => 'rse_m_legs',
        6 => 'rse_m_feet',
        7 => 'rse_f_body',
        8 => 'rse_f_hands',
        9 => 'rse_f_legs',
        10 => 'rse_f_feet',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja_male' => $line['name']['ja'],
            'name_en_male' => ucwords($line['name']['en']),
            'name_fr_male' => ucwords($line['name']['fr']),
            'name_de_male' => ucwords($line['name']['de']),

            'name_ja_female' => $line['name_female']['ja'],
            'name_en_female' => ucwords($line['name_female']['en']),
            'name_fr_female' => ucwords($line['name_female']['fr']),
            'name_de_female' => ucwords($line['name_female']['de']),
        ];
    }
}
