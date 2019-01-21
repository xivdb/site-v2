<?php

/**
 * BNpcBase
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class BNpcBase extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_npc_base';

    protected $real =
    [
        5 => 'scale',
        6 => 'model_chara',
        7 => 'b_npc_customize',
        8 => 'npc_equip',
    ];
}
