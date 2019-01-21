<?php

/**
 * DefaultTalk
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class DefaultTalk extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_default_talk';

    protected function json($line)
    {
        return
        [
            'text1_ja' => ($line['text1']['ja'] == '0') ? '' : $line['text1']['ja'],
            'text1_en' => ($line['text1']['en'] == '0') ? '' : ucwords($line['text1']['en']),
            'text1_fr' => ($line['text1']['fr'] == '0') ? '' : ucwords($line['text1']['fr']),
            'text1_de' => ($line['text1']['de'] == '0') ? '' : ucwords($line['text1']['de']),

            'text2_ja' => ($line['text2']['ja'] == '0') ? '' : $line['text2']['ja'],
            'text2_en' => ($line['text2']['en'] == '0') ? '' : ucwords($line['text2']['en']),
            'text2_fr' => ($line['text2']['fr'] == '0') ? '' : ucwords($line['text2']['fr']),
            'text2_de' => ($line['text2']['de'] == '0') ? '' : ucwords($line['text2']['de']),

            'text3_ja' => ($line['text3']['ja'] == '0') ? '' : $line['text3']['ja'],
            'text3_en' => ($line['text3']['en'] == '0') ? '' : ucwords($line['text3']['en']),
            'text3_fr' => ($line['text3']['fr'] == '0') ? '' : ucwords($line['text3']['fr']),
            'text3_de' => ($line['text3']['de'] == '0') ? '' : ucwords($line['text3']['de']),
        ];
    }
}
