<?php

/**
 * CompanyCraftPart
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CompanyCraftPart extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_company_craft_part';

    protected $real =
    [
        1 => 'company_craft_type',
        2 => 'company_craft_process_1',
        3 => 'company_craft_process_2',
        4 => 'company_craft_process_3',
    ];
}
