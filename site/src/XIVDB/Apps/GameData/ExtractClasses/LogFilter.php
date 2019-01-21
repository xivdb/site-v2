<?php

/**
 * LogFilter
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class LogFilter extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_log_filter';

    protected $real =
    [
        2 => 'log_kind',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'example_ja' => $line['example']['ja'],
            'example_en' => ucwords($line['example']['en']),
            'example_fr' => ucwords($line['example']['fr']),
            'example_de' => ucwords($line['example']['de']),
        ];
    }
}
