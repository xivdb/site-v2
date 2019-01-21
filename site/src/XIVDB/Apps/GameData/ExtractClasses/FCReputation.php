<?php

/**
 * FCReputation
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class FCReputation extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_fc_reputation';

    protected $real =
    [
        1 => 'points_to_next',
        2 => 'points_required',
    ];

    protected function json($line)
    {
        return
        [
            'text_ja' => $line['name']['ja'],
            'text_en' => ucwords($line['name']['en']),
            'text_fr' => ucwords($line['name']['fr']),
            'text_de' => ucwords($line['name']['de']),
        ];
    }
}
