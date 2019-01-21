<?php

/**
 * CraftLeveTalk
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CraftLeveTalk extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_craft_levetalk';

    protected $unknown =
    [
        37 => 'question',
        38 => 'yes',
        39 => 'no',
    ];

    protected function json($line)
    {
        return
        [
            'question_ja' => $line['question']['ja'],
            'question_en' => ucwords($line['question']['en']),
            'question_fr' => ucwords($line['question']['fr']),
            'question_de' => ucwords($line['question']['de']),

            'yes_ja' => $line['yes']['ja'],
            'yes_en' => ucwords($line['yes']['en']),
            'yes_fr' => ucwords($line['yes']['fr']),
            'yes_de' => ucwords($line['yes']['de']),

            'no_ja' => $line['no']['ja'],
            'no_en' => ucwords($line['no']['en']),
            'no_fr' => ucwords($line['no']['fr']),
            'no_de' => ucwords($line['no']['de']),
        ];
    }
}
