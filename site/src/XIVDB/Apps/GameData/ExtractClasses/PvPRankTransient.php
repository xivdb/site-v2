<?php

/**
 * PvPRank
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class PvPRankTransient extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_pvp_ranks';

    protected function json($line)
    {
        return
        [
            'storm_ja' => $line['storm']['ja'],
            'storm_en' => ucwords($line['storm']['en']),
            'storm_fr' => ucwords($line['storm']['fr']),
            'storm_de' => ucwords($line['storm']['de']),

            'serpent_ja' => $line['serpent']['ja'],
            'serpent_en' => ucwords($line['serpent']['en']),
            'serpent_fr' => ucwords($line['serpent']['fr']),
            'serpent_de' => ucwords($line['serpent']['de']),

            'flame_ja' => $line['flame']['ja'],
            'flame_en' => ucwords($line['flame']['en']),
            'flame_fr' => ucwords($line['flame']['fr']),
            'flame_de' => ucwords($line['flame']['de']),
        ];
    }
}
