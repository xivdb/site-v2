<?php

/**
 * GrandCompanyRank
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GrandCompanyRank extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gc_rank';

    protected $real =
    [
        1 => 'tier',
        2 => 'order',
        3 => 'max_seals',
        4 => 'required_seals',
        5 => 'icon_maelstrom',
        6 => 'icon_serpents',
        7 => 'icon_flames',
        8 => 'quest_maelstrom',
        9 => 'quest_serpents',
        10 => 'quest_flames',
    ];
}
