<?php

/**
 * PublicContentTextData
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class PublicContentTextData extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_public_content_text_data';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ($line['name']['en']),
            'name_fr' => ($line['name']['fr']),
            'name_de' => ($line['name']['de']),
        ];
    }
}
