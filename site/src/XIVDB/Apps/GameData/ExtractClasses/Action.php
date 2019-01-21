<?php

/**
 * Action
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Action extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_actions';

    private $help = [];

    protected $real =
    [
        3 => 'icon',
        4 => 'action_category',

        8 => 'action_timeline_use',
        9 => 'action_timeline_hit',

        11 => 'classjob',
        13 => 'level',

        15 => 'cast_range',
        16 => 'can_target_self',
        17 => 'can_target_party',
        18 => 'can_target_friendly',
        19 => 'can_target_hostile',

        22 => 'is_target_area',
        26 => 'can_target_dead',

        29 => 'effect_range',

        33 => 'cost',

        35 => 'status_required',
        36 => 'action_combo',

        38 => 'cast_time',
        39 => 'recast_time',

        43 => 'action_proc_status',
        44 => 'status_gain_self',
        45 => 'action_data',
        46 => 'classjob_category',
    ];

    protected $custom =
    [
        'type' => 1,
    ];

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

    // ---------------------------------------------

    protected function manual()
    {
        $this->handleTimers();
        $this->mergeTraits();
        $this->mergeNonAssigned();
    }

    //
    // Handle recast timers etc
    //
    private function handleTimers()
    {
        $castOffset = array_flip($this->real)['cast_time'];
        $recastOffset = array_flip($this->real)['recast_time'];

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $insert[] = [
                'id' => $id,
                'cast_time' => intval($line[$castOffset]) > 0 ? round($line[$castOffset] / 10, 3) : $line[$castOffset],
                'recast_time' => intval($line[$recastOffset]) > 0 ? round($line[$recastOffset] / 10, 3) : $line[$castOffset],
            ];
        }

        $this->insert('xiv_actions', $insert);
    }

    //
    // Merge in traits
    //
    private function mergeTraits()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->select('*')->from('xiv_traits');
        $traits = $dbs->get()->all();

        $insert = [];
        foreach($traits as $trait)
        {
            $insert[] = [
                'id' => 50000 + $trait['id'],
                'name_ja' => $trait['name_ja'],
                'name_en' => $trait['name_en'],
                'name_de' => $trait['name_de'],
                'name_cns' => $trait['name_cns'],
                'help_ja' => $trait['help_ja'],
                'help_en' => $trait['help_en'],
                'help_de' => $trait['help_de'],
                'help_fr' => $trait['help_fr'],
                'help_cns' => $trait['help_cns'],
                'icon' => $trait['icon'],
                'classjob' => $trait['classjob'],
                'classjob_category' => $trait['classjob_category'],
                'level' => $trait['level'],
                'type' => 2,
                'patch' => $trait['patch'],
            ];
        }

        $this->insert('xiv_actions', $insert);
    }

    //
    // Merge non assigned, ones with no class job but with a category
    //
    private function mergeNonAssigned()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->select('*')->from('xiv_actions');
        $actions = $dbs->get()->all();

        // get categories
        $classjobs = [];
        foreach($dbs->sql('SELECT *  FROM `xiv_classjobs_to_category` group by classjob_category;') as $cj) {
            $classjobs[$cj['classjob_category']] = $cj['classjob'];
        }

        $insert = [];
        foreach($actions as $action)
        {
            if ($action['classjob'] == '0' && $action['classjob_category'] > 0)
            {
                $cjid = $classjobs[$action['classjob_category']];

                if ($cjid > 0) {
                    $insert[] = [
                        'id' => $action['id'],
                        'classjob' => $classjobs[$action['classjob_category']],
                    ];
                }
            }
        }

        $this->insert('xiv_actions', $insert);
    }
}
