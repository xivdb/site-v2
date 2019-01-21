<?php

/**
 * GoldSaucerArcadeMachine
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GoldSaucerArcadeMachine extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_gold_saucer_arcade_machine';

    protected $real =
    [
        7 => 'icon_1', // times up etc

        36 => 'icon_2', // miss!
        37 => 'icon_3', // hit!
        38 => 'icon_4',
        39 => 'icon_5',
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
