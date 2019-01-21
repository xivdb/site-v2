<?php

/**
 * CompanionTransient
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CompanionTransient extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_companions';

    protected $real =
    [
        6 => 'attack',
        7 => 'defense',
        8 => 'speed',
        9 => 'has_area_attack',
        10 => 'strength_gate',
        11 => 'strength_eye',
        12 => 'strength_shield',
        13 => 'strength_arcana',
        14 => 'minion_skill_type',
    ];

    protected function json($line)
    {
        return
        [
            'summon_ja' => $line['summon']['ja'],
            'summon_en' => ($line['summon']['en']),
            'summon_fr' => ($line['summon']['fr']),
            'summon_de' => ($line['summon']['de']),

            'info1_ja' => $line['info1']['ja'],
            'info1_en' => ($line['info1']['en']),
            'info1_fr' => ($line['info1']['fr']),
            'info1_de' => ($line['info1']['de']),

            'info2_ja' => $line['info2']['ja'],
            'info2_en' => ($line['info2']['en']),
            'info2_fr' => ($line['info2']['fr']),
            'info2_de' => ($line['info2']['de']),

            'action_ja' => $line['action']['ja'],
            'action_en' => ($line['action']['en']),
            'action_fr' => ($line['action']['fr']),
            'action_de' => ($line['action']['de']),

            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),
        ];
    }
}
