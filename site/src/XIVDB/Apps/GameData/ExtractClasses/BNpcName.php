<?php

/**
 * BNpcName
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class BNpcName extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_npc_enemy';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'name_plural_ja' => $line['name_plural']['ja'],
            'name_plural_en' => ($line['name_plural']['en']),
            'name_plural_fr' => ($line['name_plural']['fr']),
            'name_plural_de' => ($line['name_plural']['de']),
        ];
    }
}
