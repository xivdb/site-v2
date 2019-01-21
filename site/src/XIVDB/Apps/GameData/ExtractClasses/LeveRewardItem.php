<?php

/**
 * LeveRewardItemGroup
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class LeveRewardItem extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_leves_reward_groups';

    protected $group =
    [
        [
            1 => 'reward_group',
            2 => 'probability',
        ],

        [
            3 => 'reward_group',
            4 => 'probability',
        ],

        [
            5 => 'reward_group',
            6 => 'probability',
        ],

        [
            7 => 'reward_group',
            8 => 'probability',
        ],

        [
            9 => 'reward_group',
            10 => 'probability',
        ],

        [
            11 => 'reward_group',
            12 => 'probability',
        ],

        [
            13 => 'reward_group',
            14 => 'probability',
        ],

        [
            15 => 'reward_group',
            16 => 'probability',
        ],
    ];

    protected function manual()
    {
        $this->groups();
    }

    private function groups()
    {
        // list of offsets, reward group 1 to 8
        $offsets = $this->group;

        // insert
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            foreach($offsets as $os)
            {
                $os = array_flip($os);

                $rewardGroup = $line[$os['reward_group']];
                $probability = $line[$os['probability']];

                if ($rewardGroup)
                {
                    $insert[] =
                    [
                        'id' => $id,
                        'group' => $rewardGroup,
                        'probability' => $probability,
                    ];
                }
            }
        }

        $this->insert(self::TABLE, $insert);
    }
}
