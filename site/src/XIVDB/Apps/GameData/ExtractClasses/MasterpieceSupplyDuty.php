<?php

/**
 * MasterpieceSupplyDuty
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class MasterpieceSupplyDuty extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_masterpiece_supply_duty';

    protected $real =
    [
        1 => 'classjob',
        2 => 'item_level',
        3 => 'reward_item',
    ];

    protected $repeater =
    [
        'start' => 4,
        'finish' => 83,

        'columns' => [
            'required_item',
            'quantity',
            'request_hq',
            'collectability_high_bonus',
            'collectability_bonus',
            'collectability_base',
            'reward_factory',
            'reward_base',
            'recipe_level',
            'stars',
        ],
    ];
}
