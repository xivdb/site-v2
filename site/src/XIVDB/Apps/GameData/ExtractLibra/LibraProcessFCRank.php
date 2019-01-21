<?php

/**
 * LibraProcessFCRank
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessFCRank extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_fc_rank';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [
                'current_points' => $d['CurrentPoint'],
                'next_points' => $d['NextPoint'],
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
