<?php

/**
 * ENpcBase
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ENpcBase extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = false;

    protected $set =
    [
        0 => 'npc',

        1 => 'data_1',
        2 => 'data_2',
        3 => 'data_3',
        4 => 'data_4',
        5 => 'data_5',
        6 => 'data_6',
        7 => 'data_7',
        8 => 'data_8',
        9 => 'data_9',
        10 => 'data_10',
        11 => 'data_11',
        12 => 'data_12',
        13 => 'data_13',
        14 => 'data_14',
        15 => 'data_15',
        16 => 'data_16',
        17 => 'data_17',
        18 => 'data_18',
        19 => 'data_19',
        20 => 'data_20',
        21 => 'data_21',
        22 => 'data_22',
        23 => 'data_23',
        24 => 'data_24',
        25 => 'data_25',
        26 => 'data_26',
        27 => 'data_27',
        28 => 'data_28',
        29 => 'data_29',
        30 => 'data_30',
    ];

    protected function manual()
    {
        $this->data();
    }

    private function data()
    {
        // list of offsets
        $arr = array_keys($this->set);
        unset($arr[0]);

        // insert
        $shops = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            foreach($arr as $os)
            {
                $dataId = $line[$os];
                if ($dataId)
                {
                    /**
                     * 262 = shop or housing preset
                     * 589-592 = default talk
                     * 65-66 = quest
                     * 131 = warp
                     * 393 = guildleve assignment
                     * 720-721 = custom talk
                     */

                    // if shop
                    if (substr($dataId, 0, 3) == '262')
                    {
                        $shops[] =
                        [
                            'npc' => $id,
                            'shop' => $dataId,
                        ];
                    }
                }
            }
        }

        $this->insert('xiv_npc_to_shop', $shops);
    }
}
