<?php

/**
 * SpecialShop
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class SpecialShop extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_special_shops';

    protected $truncate = [
        'xiv_special_shops_to_item'
    ];

    protected $real =
    [
        1743 => 'quest_shop',
    ];

    protected $special =
    [
        'receive_item_1' => [2, 61],
        'receive_count_1' => [62, 121],
        'receive_special_shop_item_category_1' => [122, 181],
        'receive_hq_1' => [182, 241],

        'receive_item_2' => [242, 301],
        'receive_count_2' => [302, 361],
        'receive_special_shop_item_category_2' => [362, 421],
        'receive_hq_2' => [422, 481],

        'cost_item_1' => [482, 541],
        'cost_count_1' => [542, 601],
        'cost_hq_1' => [602, 661],
        'cost_collectability_rating_1' => [662, 721],

        'cost_item_2' => [722, 781],
        'cost_count_2' => [782, 841],
        'cost_h1_2' => [842, 901],
        'cost_collectability_rating_2' => [902, 961],

        'cost_item_3' => [962, 1021],
        'cost_count_3' => [1022, 1081],
        'cost_hq_3' => [1082, 1141],
        'cost_collectability_rating_3' => [1142, 1201],

        'quest_item_1' => [1202, 1261],

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

    protected function manual()
    {
        $this->addItems();
    }

    //
    // Add special shop items into the db
    //
    protected function addItems()
    {
        $insert = [];

        // Loop through special shops
        foreach($this->getCsvFileData() as $shopId => $line)
        {
            $temp = [];
            foreach($this->special as $key => $offsets)
            {
                // Make temp[key] an array
                if (!isset($temp[$key])) {
                    $temp[$key] = [];
                }

                // Get all values between offset
                for ($i = $offsets[0]; $i <= $offsets[1]; $i++) {
                    $temp[$key][] = $line[$i];
                }
            }

            // loop through temp
            $temp2 = [];
            foreach($temp as $key => $list)
            {
                foreach($list as $i => $value)
                {
                    if (!isset($temp2[$i])) {
                        $temp2[$i] = [
                            'special_shop' => $shopId,
                        ];
                    };

                    $temp2[$i][$key] = $value;
                }
            }

            $insert = array_merge($insert, $temp2);
        }

        $this->insert('xiv_special_shops_to_item', $insert);
    }
}
