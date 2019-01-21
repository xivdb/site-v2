<?php

/**
 * AirshipExplorationPoint
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class AirshipExplorationPoint extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_airship_exploration_point';

    protected $real =
    [
        6 => 'required_level',
        7 => 'required_fuel',
        8 => 'duration_min',
        14 => 'exp_reward',
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
