<?php

/**
 * ClassJob
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ClassJob extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_classjobs';

    protected $real =
    [
        4 => 'classjob_category',
        27 => 'classjob_parent',
        29 => 'item_starting_weapon',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'abbr_ja' => $line['abbr']['ja'],
            'abbr_en' => $line['abbr']['en'],
            'abbr_fr' => $line['abbr']['fr'],
            'abbr_de' => $line['abbr']['de'],

            'is_job' => $line['is_job'],
            'icon' => strtolower(str_ireplace(' ', null, $line['name']['en'])),
        ];
    }
}
