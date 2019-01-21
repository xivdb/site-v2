<?php

/**
 * JournalGenre
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class JournalGenre extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_journal_genre';

    protected $real =
    [
        1 => 'icon',
        2 => 'journal_category',
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
