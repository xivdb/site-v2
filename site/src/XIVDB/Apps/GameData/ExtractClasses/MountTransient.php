<?php

/**
 * MountTransient
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class MountTransient extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_mounts';

    protected function json($line)
    {
        return
        [
            'summon_ja' => $line['summon']['ja'],
            'summon_en' => $line['summon']['en'],
            'summon_fr' => $line['summon']['fr'],
            'summon_de' => $line['summon']['de'],

            'info1_ja' => $line['info1']['ja'],
            'info1_en' => $line['info1']['en'],
            'info1_fr' => $line['info1']['fr'],
            'info1_de' => $line['info1']['de'],

            'info2_ja' => $line['info2']['ja'],
            'info2_en' => $line['info2']['en'],
            'info2_fr' => $line['info2']['fr'],
            'info2_de' => $line['info2']['de'],
        ];
    }
}
