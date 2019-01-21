<?php

/**
 * LibraProcessClassJobClassJobCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessClassJobClassJobCategory extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_classjobs_to_category';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $new =
            [
                'classjob' => $d['ClassJob_Key'],
                'classjob_category' => $d['ClassJobCategory_Key'],
            ];

            $new = array_merge($new, ['patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
