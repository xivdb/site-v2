<?php

/**
 * LeveAssignmentType
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class LeveAssignmentType extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_leves_assignment_type';

    protected $real =
    [
        1 => 'is_faction',
        2 => 'icon',
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
