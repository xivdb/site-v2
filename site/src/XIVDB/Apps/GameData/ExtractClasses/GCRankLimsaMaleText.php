<?php

/**
 * GCRankLimsaMaleText
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GCRankLimsaMaleText extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_gc_rank_limsa_male_text';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'rank_ja' => $line['rank']['ja'],
            'rank_en' => ucwords($line['rank']['en']),
            'rank_fr' => ucwords($line['rank']['fr']),
            'rank_de' => ucwords($line['rank']['de']),
        ];
    }
}
