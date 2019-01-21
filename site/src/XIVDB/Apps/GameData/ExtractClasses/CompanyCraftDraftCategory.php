<?php

/**
 * CompanyCraftDraftCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CompanyCraftDraftCategory extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_company_craftdraft_category';

    protected $real =
    [
        2 => 'company_craft_type_1',
        3 => 'company_craft_type_2',
        4 => 'company_craft_type_3',
        5 => 'company_craft_type_4',
        6 => 'company_craft_type_5',
        7 => 'company_craft_type_6',
        8 => 'company_craft_type_7',
        9 => 'company_craft_type_8',
        10 => 'company_craft_type_9',
        11 => 'company_craft_type_10',
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
