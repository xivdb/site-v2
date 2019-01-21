<?php

/**
 * RelicItem
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class RelicItem extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_relic_item';

    protected $real =
    [
        2 => 'gladiator',
        3 => 'pugilist',
        4 => 'marauder',
        5 => 'lancer',
        6 => 'archer',
        7 => 'conjurer',
        8 => 'thaumaturge',
        9 => 'arcanist_1',
        10 => 'arcanist_2',
        11 => 'shield',
        12 => 'rogue',
    ];
}
