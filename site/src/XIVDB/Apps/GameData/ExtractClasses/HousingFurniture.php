<?php

/**
 * HousingFurniture
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class HousingFurniture extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_housing_furniture';

    protected $real =
    [
        1 => 'model_key',
        2 => 'housing_item_category',
        3 => 'usage_type',
        4 => 'usage_parameter',
        5 => 'housing_layout_limit',
        6 => 'event',
        7 => 'item',
        8 => 'destroy_on_removal',
    ];
}
