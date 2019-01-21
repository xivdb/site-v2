<?php

/**
 * GCShop
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GCShop extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_gc_shop';

    protected $real =
    [
        //1 => 'min',
        //2 => 'max',
        1 => 'grand_company',
    ];
}
