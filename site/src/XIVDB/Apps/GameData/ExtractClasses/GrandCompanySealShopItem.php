<?php

/**
 * GrandCompanySealShopItem
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GrandCompanySealShopItem extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gc_seal_shop_item';

    protected $real =
    [
        1 => 'item',
        2 => 'grand_company_rank',
        3 => 'count',
        4 => 'cost',
        5 => 'gc_shop_item_category',
    ];
}
