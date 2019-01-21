<?php

/**
 * LibraProcessQuestClassJob
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessQuestClassJob extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_quests_to_classjob';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $new = [
                'quest' => $d['Quest_Key'],
                'classjob' => $d['ClassJob_Key']
            ];

            $new = array_merge($new, ['patch' => $this->patch]);
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
