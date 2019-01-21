<?php

/**
 * RecipeLevelTable
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class RecipeLevelTable extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_recipes_level';

    protected $real =
    [
        1 => 'character_level',
        2 => 'stars',
        // ???
        4 => 'difficulty',
        5 => 'quality',
        6 => 'durability',
    ];
}
