<?php

/**
 * LibraProcessGCRankGridaniaFemaleText
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGCRankGridaniaFemaleText extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_gc_title_gridania';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'name_ja_female' => $d['SGL_ja'],
                'name_en_female' => $d['SGL_en'],
                'name_fr_female' => $d['SGL_fr'],
                'name_de_female' => $d['SGL_de'],
                'name_cns_female' => null,
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
