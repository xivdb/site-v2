<?php

/**
 * FishingSpot
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class FishingSpot extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_fishing_spot';

    protected $real =
    [
        4 => 'fishing_spot_category',
        6 => 'territory_id',
        7 => 'pos_x',
        8 => 'pos_z',
        9 => 'radius',
        21 => 'placename',
    ];

    protected $set =
    [
        11 => 'item_1',
        12 => 'item_2',
        13 => 'item_3',
        14 => 'item_4',
        15 => 'item_5',
        16 => 'item_6',
        17 => 'item_7',
        18 => 'item_8',
        19 => 'item_9',
        20 => 'item_10',
    ];

    protected function json($line)
    {
        return
        [
            'big_fish_on_reach_ja' => $line['big_fish_on_reach']['ja'],
            'big_fish_on_reach_en' => ucwords($line['big_fish_on_reach']['en']),
            'big_fish_on_reach_fr' => ucwords($line['big_fish_on_reach']['fr']),
            'big_fish_on_reach_de' => ucwords($line['big_fish_on_reach']['de']),

            'big_fish_on_end_ja' => $line['big_fish_on_end']['ja'],
            'big_fish_on_end_en' => ucwords($line['big_fish_on_end']['en']),
            'big_fish_on_end_fr' => ucwords($line['big_fish_on_end']['fr']),
            'big_fish_on_end_de' => ucwords($line['big_fish_on_end']['de']),
        ];
    }
}
