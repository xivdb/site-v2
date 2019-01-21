<?php

/**
 * CraftType
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CraftType extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_craft_type';

    protected $real =
    [
        1 => 'craft_crystal_type_primary',
        2 => 'craft_crystal_type_secondary',
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
