<?php

/**
 * LibraProcessAchievementKind
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessAchievementKind extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_achievements_kind';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $insert[] = array_merge($this->names($d), ['id' => $id, 'patch' => $this->patch]);
        }

        return $this->insert($table, $insert);
    }
}
