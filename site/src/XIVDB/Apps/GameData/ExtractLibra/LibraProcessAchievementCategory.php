<?php

/**
 * LibraProcessAchievementCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessAchievementCategory extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_achievements_category';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new =
            [
                'kind' => $d['Kind'],
            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
