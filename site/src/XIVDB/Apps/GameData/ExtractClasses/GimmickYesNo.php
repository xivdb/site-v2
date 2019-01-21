<?php

/**
 * GimmickYesNo
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GimmickYesNo extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gimmick_yesno';

    protected $unknown =
    [
        1 => 'question',
        2 => 'option1',
        3 => 'option2',
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
