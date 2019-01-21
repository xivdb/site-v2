<?php

/**
 * Role
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Role extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_roles';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'letter_ja' => $line['letter']['ja'],
            'letter_en' => ucwords($line['letter']['en']),
            'letter_fr' => ucwords($line['letter']['fr']),
            'letter_de' => ucwords($line['letter']['de']),
        ];
    }
}
