<?php

/**
 * Relic
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Relic extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_relic';

    protected $real =
    [
        1 => 'item_atma',
        2 => 'item_animus',
        3 => 'icon',
    ];
}
