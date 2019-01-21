<?php

/**
 * Completion
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Completion extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_completion';

    protected $unknown =
    [
        1 => 'name',
        2 => 'code',
        3 => 'group' ,
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'group_ja' => $line['group']['ja'],
            'group_en' => ($line['group']['en']),
            'group_fr' => ($line['group']['fr']),
            'group_de' => ($line['group']['de']),
        ];
    }
}
