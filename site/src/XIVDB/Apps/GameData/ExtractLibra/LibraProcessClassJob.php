<?php

/**
 * LibraProcessClassJob
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessClassJob extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_classjobs';

        $insert = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new =
            [
                'id2' => $d['Key'],
                'icon' => str_ireplace(' ', null, strtolower($d['Name_en'])),

                'abbr_ja' => $d['Abbreviation_ja'],
                'abbr_en' => $d['Abbreviation_en'],
                'abbr_fr' => $d['Abbreviation_fr'],
                'abbr_de' => $d['Abbreviation_de'],
                'abbr_cns' => null,

                'is_job' => $d['IsJob'],

                'filebase' => $d['filebase'],
            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        /*
        $insert[] =
        [
            'id2' => 0,
            'icon' => 'adventurer',

            'abbr_ja' => 'ADV',
            'abbr_en' => 'ADV',
            'abbr_fr' => 'AVN',
            'abbr_de' => 'ABE',
            'abbr_cns' => null,

            'is_job' => 0,
            'priority' => 0,
            'filebase' => null,

            'name_ja' => 'すっぴん士',
            'name_en' => 'Adventurer',
            'name_fr' => 'Abenteurer',
            'name_de' => 'Aventurier',


            'id' => 1,
            'patch' => 1,
        ];
        */

        return $this->insert($table, $insert);
    }
}
