<?php

/**
 * InstanceContentTextData
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class InstanceContentTextData extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_instances_text_data';

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
