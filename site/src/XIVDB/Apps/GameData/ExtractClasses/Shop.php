<?php

/**
 * Shop
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Shop extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_shops';

    private $itemids = [];

    protected $real =
    [
        2 => 'icon',
        3 => 'beast_tribe',
        4 => 'beast_reputation_rank',
        5 => 'quest',
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
