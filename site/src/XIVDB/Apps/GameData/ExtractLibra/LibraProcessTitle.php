<?php

/**
 * LibraProcessTitle
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessTitle extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_titles';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'name_ja_male' => $d['Male_ja'],
                'name_en_male' => $d['Male_en'],
                'name_fr_male' => $d['Male_fr'],
                'name_de_male' => $d['Male_de'],
                'name_cns_male' => null,
                'name_ja_female' => $d['Female_ja'],
                'name_en_female' => $d['Female_en'],
                'name_fr_female' => $d['Female_fr'],
                'name_de_female' => $d['Female_de'],
                'name_cns_female' => null,
                'front_ja' => $d['Front_ja'],
                'front_en' => $d['Front_en'],
                'front_fr' => $d['Front_fr'],
                'front_de' => $d['Front_de'],
                'front_cns' => null,
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
