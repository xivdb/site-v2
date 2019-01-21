<?php

/**
 * Adventure
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Adventure extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_adventures';

    protected $real =
    [
        1 => 'level',
        4 => 'emote',
        5 => 'min_time',
        6 => 'max_time',
        7 => 'placename',
        8 => 'icon_list',
        9 => 'icon_discovered',
        13 => 'icon_undiscovered',
        14 => 'is_initial',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'text1_ja' => $line['text1']['ja'],
            'text1_en' => ($line['text1']['en']),
            'text1_fr' => ($line['text1']['fr']),
            'text1_de' => ($line['text1']['de']),

            'text2_ja' => $line['text2']['ja'],
            'text2_en' => ($line['text2']['en']),
            'text2_fr' => ($line['text2']['fr']),
            'text2_de' => ($line['text2']['de']),
        ];
    }
}
