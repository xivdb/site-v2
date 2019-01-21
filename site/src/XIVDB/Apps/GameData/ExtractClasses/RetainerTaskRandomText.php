<?php

/**
 * RetainerTaskRandomText
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class RetainerTaskRandomText extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_retainer_task_random_text';

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
