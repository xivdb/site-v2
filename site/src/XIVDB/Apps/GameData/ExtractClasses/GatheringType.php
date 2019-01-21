<?php

/**
 * GatheringType
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringType extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gathering_type';

    protected $real =
    [
        2 => 'icon_main',
        3 => 'icon_off',
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
