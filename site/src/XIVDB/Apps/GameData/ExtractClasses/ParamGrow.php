<?php

/**
 * ParamGrow
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ParamGrow extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_param_grow';

    protected $real =
    [
        0 => 'level',

        1 => 'exp',
        2 => 'additional_actions',
        3 => 'attribute_bonus_class',
        4 => 'attribute_bonus_job',
        5 => 'mp_cost_percent',

        8 => 'quest_exp_modifier',
        10 => 'hunting_log_reward_exp'
    ];
}
