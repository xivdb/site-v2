<?php

/**
 * LibraProcessFCHierarchy
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessFCHierarchy extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_fc_hierarchy';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
