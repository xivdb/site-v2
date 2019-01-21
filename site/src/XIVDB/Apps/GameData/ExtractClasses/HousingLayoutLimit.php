<?php

/**
 * HousingLayoutLimit
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class HousingLayoutLimit extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_housing_layout_limit';

    protected $real =
    [
        1 => 'personal_chamber',
        2 => 'small',
        3 => 'medium',
        4 => 'large',
    ];
}
