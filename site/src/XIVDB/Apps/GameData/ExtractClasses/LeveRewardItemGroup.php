<?php

/**
 * LeveRewardItem
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class LeveRewardItemGroup extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_leves_reward_items';

    protected $group =
    [
        [
            1 => 'item',
            2 => 'quantity',
            3 => 'hq',
        ],

        [
            4 => 'item',
            5 => 'quantity',
            6 => 'hq',
        ],

        [
            7 => 'item',
            8 => 'quantity',
            9 => 'hq',
        ],

        [
            10 => 'item',
            11 => 'quantity',
            12 => 'hq',
        ],

        [
            13 => 'item',
            14 => 'quantity',
            15 => 'hq',
        ],

        [
            16 => 'item',
            17 => 'quantity',
            18 => 'hq',
        ],

        [
            19 => 'item',
            20 => 'quantity',
            21 => 'hq',
        ],

        [
            22 => 'item',
            23 => 'quantity',
            24 => 'hq',
        ],

        [
            25 => 'item',
            26 => 'quantity',
            27 => 'hq',
        ],
    ];

    protected function manual()
    {
        $this->items();
    }

    private function items()
    {
        $offsets = $this->group;

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            foreach($offsets as $os)
            {
                $os = array_flip($os);

                $item = $line[$os['item']];
                $quantity = $line[$os['quantity']];
                $hq = $line[$os['hq']];

                if ($item)
                {
                    $insert[] =
                    [
                        'id' => $id,
                        'item' => $item,
                        'quantity' => $quantity,
                        'hq' => $hq,
                    ];
                }
            }
        }

        $this->insert(self::TABLE, $insert);
    }
}
