<?php

/**
 * MonsterNote
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class MonsterNote extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_hunting_log';

    protected $real =
    [
        1 => 'target_1',
        2 => 'target_2',
        3 => 'target_3',
        4 => 'target_4',

        5 => 'quantity_1',
        6 => 'quantity_2',
        7 => 'quantity_3',
        8 => 'quantity_4',

        9 => 'exp_reward',
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
