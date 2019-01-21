<?php

/**
 * ChocoboRace
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ChocoboRace extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_chocobo_race';

    protected $real =
    [
        1 => 'chocobo_race_rank',
        2 => 'chocobo_race_territory',
    ];
}
