<?php

/**
 * LibraProcessQuestWebEx
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessQuestWebEx extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_quests_webex';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [];

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
