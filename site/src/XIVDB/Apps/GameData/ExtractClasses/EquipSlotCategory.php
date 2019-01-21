<?php

/**
 * EquipSlotCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class EquipSlotCategory extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_equip_slot_category';

    protected $real =
    [
        1 => 'mainhand',
        2 => 'offhand',
        3 => 'head',
        4 => 'body',
        5 => 'gloves',
        6 => 'waist',
        7 => 'legs',
        8 => 'feet',
        9 => 'ears',
        10 => 'neck',
        11 => 'wrists',
        12 => 'ringleft',
        13 => 'ringright',
        13 => 'souldcrystal',
    ];
}
