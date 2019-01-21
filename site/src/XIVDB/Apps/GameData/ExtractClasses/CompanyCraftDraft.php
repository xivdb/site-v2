<?php

/**
 * CompanyCraftDraft
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CompanyCraftDraft extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_company_craft_draft';

    protected $real =
    [
        2 => 'company_craft_draft_category',
        9 => 'order',
    ];

    protected $set =
    [
        3 => 'item_1',
        4 => 'item_1_quantity',
        5 => 'item_2',
        6 => 'item_2_quantity',
        7 => 'item_3',
        8 => 'item_3_quantity',
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
