<?php

/**
 * ItemUICategory
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ItemUICategory extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_items_ui_category';

    protected $real =
    [
        2 => 'icon',
        3 => 'order_minor',
        4 => 'order_major',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),
        ];
    }
}
