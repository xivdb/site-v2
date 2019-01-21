<?php

/**
 * LibraProcessGeneralAction
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGeneralAction extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_actions_general';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new =
            [
                'icon' => $d['Icon'],
            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }
        }

        return $this->insert($table, $insert);
    }
}
