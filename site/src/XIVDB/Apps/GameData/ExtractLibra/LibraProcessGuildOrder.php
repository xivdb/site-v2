<?php

/**
 * LibraProcessGuildOrder
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGuildOrder extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_gc_guildorder';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'exp' => $d['Exp00'],
                'exp_2' => $d['Exp01'],
                'gil' => $d['Gil00'],
                'gil_2' => $d['Gil01'],
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
