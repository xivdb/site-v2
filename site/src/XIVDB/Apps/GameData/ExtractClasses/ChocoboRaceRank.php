<?php

/**
 * ChocoboRaceRank
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ChocoboRaceRank extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_chocobo_race_rank';

    protected $real =
    [
        1 => 'rating_min',
        2 => 'rating_max',
        4 => 'fee',
        5 => 'icon',
    ];
}
