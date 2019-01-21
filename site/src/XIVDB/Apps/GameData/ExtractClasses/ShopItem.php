<?php

/**
 * ShopItem
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ShopItem extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_shops_item';

    protected $real =
    [
        1 => 'item',
        3 => 'price_factor_low',
        4 => 'price_factor_mid',
        5 => 'price_factor_high',
        7 => 'quest',
        9 => 'achievement',
    ];

    public function manual()
    {
        $this->shopsToItem();
    }

    private function shopsToItem()
    {
        $offsets = array_flip($this->real);
        $shops = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'Shop.csv');
        $shopItems = $this->getCsvFileData();
        $shopItemsOffsets = [
            8,9,10,11,12,13,14,15,16,17,18,
            19,20,21,22,23,24,25,26,27,28,29,
            30,31,32,33,34,35,36,37,38,39,40,
            41,42,43,44,45,46,47
        ];

        $insert = [];
        foreach($shops as $id => $line)
        {
            foreach($shopItemsOffsets as $os)
            {
                $shopItemsId = $line[$os];
                if ($shopItemsId == 0) continue;

                // get item
                $item = isset($shopItems[$shopItemsId]) ? $shopItems[$shopItemsId] : false;
                if (!$item) {
                    return;
                }

                $insert[] = [
                    'shop' => $id,
                    'item' => $item[$offsets['item']],
                    'quest' => $item[$offsets['quest']],
                    'achievement' => $item[$offsets['achievement']],
                ];
            }
        }

        $this->insert('xiv_shops_to_item', $insert);
    }
}
