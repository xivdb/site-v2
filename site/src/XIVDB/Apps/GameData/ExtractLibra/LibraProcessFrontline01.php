<?php

/**
 * LibraProcessFrontline01
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessFrontline01 extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_pvp_frontline';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $exp = json_decode($d['WinPvPExp'], true);
            $token = json_decode($d['WinPvPToken'], true);

            $new = [
                'win_exp_1st' => $exp[0],
                'win_exp_2nd' => $exp[1],
                'win_exp_3rd' => $exp[2],
                'win_token_1st' => $token[0],
                'win_token_2nd' => $token[1],
                'win_token_3rd' => $token[2],
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
