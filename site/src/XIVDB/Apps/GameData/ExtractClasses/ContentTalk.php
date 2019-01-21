<?php

/**
 * ContentTalk
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ContentTalk extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_contents_talk';

    protected function json($line)
    {
        return
        [
            'text_ja' => $line['text']['ja'],
            'text_en' => ($line['text']['en']),
            'text_fr' => ($line['text']['fr']),
            'text_de' => ($line['text']['de']),
        ];
    }
}
