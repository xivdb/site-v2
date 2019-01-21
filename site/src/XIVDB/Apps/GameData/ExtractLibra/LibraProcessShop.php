<?php

/**
 * LibraProcessShop
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessShop extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_shops';

        $insert = [];
        $items = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'beast_tribe' => $d['BeastTribe'],
            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
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
