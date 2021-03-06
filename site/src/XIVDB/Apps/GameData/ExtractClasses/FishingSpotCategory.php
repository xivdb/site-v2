<?php

/**
 * FishingSpotCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class FishingSpotCategory extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_fishing_spot_category';

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
