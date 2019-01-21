<?php

/**
 * CustomTalk
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CustomTalk extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_custom_talk';

    protected function json($line)
    {
        return
        [
            'text_ja' => $line['text']['ja'],
            'text_en' => ucwords($line['text']['en']),
            'text_fr' => ucwords($line['text']['fr']),
            'text_de' => ucwords($line['text']['de']),
        ];
    }
}
