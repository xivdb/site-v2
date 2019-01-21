<?php

/**
 * Leve
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Leve extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_leves';

    protected $real =
    [
        3 => 'leve_client',
        4 => 'leve_assignment_type',
        6 => 'class_level',
        10 => 'placename',
        13 => 'classjob_category',
        17 => 'icon_city_state',
        18 => 'data',
        22 => 'exp_reward',
        23 => 'gil_reward',
        24 => 'leve_reward_group',
        25 => 'leve_vfx',
        26 => 'leve_vfx_frame',
        28 => 'icon_issuer',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'help_ja' => $line['help']['ja'],
            'help_en' => $line['help']['en'],
            'help_fr' => $line['help']['fr'],
            'help_de' => $line['help']['de'],
        ];
    }

    protected function manual()
    {
        $this->delete();
    }

    private function delete()
    {
        $list = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20];
        $list = implode(',', $list);

        $dbs = $this->getModule('database');
        
        $dbs->QueryBuilder
            ->delete('xiv_leves')
            ->where(sprintf('id IN (%s)', $list));

        $dbs->execute();
    }
}
