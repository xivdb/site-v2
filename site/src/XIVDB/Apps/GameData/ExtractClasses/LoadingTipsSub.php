<?php

/**
 * LoadingTipsSub
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class LoadingTipsSub extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_loading_tips_sub';

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
