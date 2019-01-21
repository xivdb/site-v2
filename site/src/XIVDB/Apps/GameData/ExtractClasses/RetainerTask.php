<?php

/**
 * RetainerTask
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class RetainerTask extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_retainer_task';

    protected $real =
    [
        1 => 'is_random',
        2 => 'classjob_category',
        3 => 'level',
        5 => 'venture_cost',
        6 => 'max_timemin',
        7 => 'experience',
        8 => 'required_item_level',
        11 => 'required_gathering',
        13 => 'task',
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
