<?php

/**
 * Relic3
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Relic3 extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_relic3';

    protected $real =
    [
        1 => 'item',
        2 => 'item_scroll',
        3 => 'materia_limit',
        4 => 'item_novus',
        5 => 'icon',
    ];
}
