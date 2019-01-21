<?php

/**
 * LibraProcessGrandCompany
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGrandCompany extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_gc';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [];

            $new = array_merge($new, $this->names($d, 'SGL'), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
