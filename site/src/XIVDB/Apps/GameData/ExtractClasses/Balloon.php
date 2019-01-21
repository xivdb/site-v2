<?php

/**
 * Balloon
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Balloon extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_balloons';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ($line['name']['en']),
            'name_fr' => ($line['name']['fr']),
            'name_de' => ($line['name']['de']),
        ];
    }
}
