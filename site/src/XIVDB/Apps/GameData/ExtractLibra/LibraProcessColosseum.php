<?php

/**
 * LibraProcessColosseum
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessColosseum extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_pvp_colosseum';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new =
            [
                'win_exp'       => $d['WinExp'],
                'win_points'    => $d['WinPoint'],
                'win_token'     => $d['WinToken'],
                'lose_points'   => $d['LosePoint'],
                'lose_token'    => $d['LoseToken'],
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
