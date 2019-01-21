<?php

/**
 * GatheringSubCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringSubCategory extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gathering_sub_category';

    protected $real =
    [
        6 => 'folklore_book',
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
