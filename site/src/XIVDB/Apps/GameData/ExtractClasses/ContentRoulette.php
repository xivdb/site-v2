<?php

/**
 * ContentRoulette
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ContentRoulette extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_contents_roulette';

    protected $real =
    [
        5 => 'is_in_duty_finder',
        6 => 'require_all_duties',
        8 => 'item_level',
        9 => 'icon',

        16 => 'reward_tome_a',
        17 => 'reward_tome_b',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),

            'type_ja' => $line['type']['ja'],
            'type_en' => ($line['type']['en']),
            'type_fr' => ($line['type']['fr']),
            'type_de' => ($line['type']['de']),
        ];
    }
}
