<?php

/**
 * LibraProcessGCRankGridaniaMaleText
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGCRankGridaniaMaleText extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_gc_title_gridania';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'name_ja_male' => $d['SGL_ja'],
                'name_en_male' => $d['SGL_en'],
                'name_fr_male' => $d['SGL_fr'],
                'name_de_male' => $d['SGL_de'],
                'name_cns_male' => null,
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
