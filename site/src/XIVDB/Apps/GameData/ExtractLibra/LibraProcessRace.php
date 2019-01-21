<?php

/**
 * LibraProcessRace
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessRace extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_races';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'name_ja_male' => $d['Name_ja'],
                'name_en_male' => $d['Name_en'],
                'name_fr_male' => $d['Name_fr'],
                'name_de_male' => $d['Name_de'],
                'name_cns_male' => null,
                'name_ja_female' => $d['NameFemale_ja'],
                'name_en_female' => $d['NameFemale_en'],
                'name_fr_female' => $d['NameFemale_fr'],
                'name_de_female' => $d['NameFemale_de'],
                'name_cns_female' => null,
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }
        }

        $this->insert($table, $insert);

        return true;
    }
}
