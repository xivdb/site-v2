<?php

/**
 * LogKind
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class LogKind extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_log_kind';

    protected $real =
    [
        5 => 'log_kind_category_text',
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
