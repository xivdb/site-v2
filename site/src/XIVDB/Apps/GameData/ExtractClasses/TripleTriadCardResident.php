<?php

/**
 * TripleTriadCardResident
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class TripleTriadCardResident extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_triple_triad_card_resident';

    protected $real =
    [
        2 => 'top',
        3 => 'bottom',
        4 => 'left',
        5 => 'right',
        6 => 'rarity',
        7 => 'type',
        8 => 'sale_value',
        9 => 'sort'
    ];
}
