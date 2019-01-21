<?php

/**
 * AirshipExplorationParamType
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class AirshipExplorationParamType extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_airship_exploration_paramtype';

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
