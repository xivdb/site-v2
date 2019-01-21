<?php

/**
 * CompanyCraftSequence
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CompanyCraftSequence extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_company_craft_sequence';

    protected $real =
    [
        1 => 'result_item',
        3 => 'company_craft_draft_category',
        4 => 'company_craft_type',
        5 => 'company_craft_draft',
    ];

    protected $set =
    [
        6 => 'company_craft_part_1',
        7 => 'company_craft_part_2',
        8 => 'company_craft_part_3',
        9 => 'company_craft_part_4',
        10 => 'company_craft_part_5',
        11 => 'company_craft_part_6',
        12 => 'company_craft_part_7',
        13 => 'company_craft_part_8',
    ];
}
