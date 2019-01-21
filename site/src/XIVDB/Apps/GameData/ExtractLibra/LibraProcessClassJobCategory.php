<?php

/**
 * LibraProcessClassJobCategory
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessClassJobCategory extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_classjobs_category';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $new = array_merge($this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
