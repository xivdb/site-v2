<?php

/**
 * ItemSearchCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ItemSearchCategory extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_items_search_category';


    protected $real =
    [
        2 => 'icon',
        3 => 'category',
        4 => 'order',
        5 => 'classjob',
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
