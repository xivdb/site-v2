<?php

/**
 * BuddyEquip
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class BuddyEquip extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_buddy_equip';

    protected $real =
    [
        10 => 'model_top',
        11 => 'model_body',
        12 => 'model_legs',
        13 => 'grand_company',
        14 => 'icon_head',
        15 => 'icon_body',
        16 => 'icon_legs',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'item_ja' => $line['item']['ja'],
            'item_en' => ($line['item']['en']),
            'item_fr' => ($line['item']['fr']),
            'item_de' => ($line['item']['de']),

            'item_plural_ja' => $line['item_plural']['ja'],
            'item_plural_en' => ($line['item_plural']['en']),
            'item_plural_fr' => ($line['item_plural']['fr']),
            'item_plural_de' => ($line['item_plural']['de']),
        ];
    }
}
