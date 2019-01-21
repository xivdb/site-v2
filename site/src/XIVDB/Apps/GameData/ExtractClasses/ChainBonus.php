<?php

/**
 * ChainBonus
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ChainBonus extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_chain_bonus';

    protected $real =
    [
        1 => 'bonus',
        2 => 'timeout',
    ];
}
