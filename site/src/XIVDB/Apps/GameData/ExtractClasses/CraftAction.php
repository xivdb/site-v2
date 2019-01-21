<?php

/**
 * CraftAction
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class CraftAction extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_craft_action';

    protected $real =
    [
        5 => 'icon',
        6 => 'classjob',
        7 => 'classjob_category',
        8 => 'level',
        12 => 'cp_cost',
    ];

	protected $custom =
    [
        'type' => 3,
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

    protected function manual()
    {
        $insert = [];
        $json = $this->getJsonFileData();

        foreach($this->getCsvFileData() as $id => $line)
        {
            $insert[] = [
                'id' => $id,

                'name_ja' => $json[$id]['name']['ja'],
                'name_en' => $json[$id]['name']['en'],
                'name_fr' => $json[$id]['name']['fr'],
                'name_de' => $json[$id]['name']['de'],
                'help_ja' => $json[$id]['help']['ja'],
                'help_en' => $json[$id]['help']['en'],
                'help_fr' => $json[$id]['help']['fr'],
                'help_de' => $json[$id]['help']['de'],

                'icon' => $line[array_flip($this->real)['icon']],
                'classjob' => $line[array_flip($this->real)['classjob']],
                'classjob_category' => $line[array_flip($this->real)['classjob_category']],
                'level' => $line[array_flip($this->real)['level']],
                'cost_cp' => $line[array_flip($this->real)['cp_cost']],
				'type' => 3,

                'patch' => $this->patch,
            ];
        }

        $this->insert('xiv_actions', $insert);
    }
}
