<?php

/**
 * TraitTransient
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class TraitTransient extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_traits';

    protected function json($line)
    {
        return
        [
            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),
        ];
    }
}
