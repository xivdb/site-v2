<?php

/**
 * LibraProcessGatheringType
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGatheringType extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_gathering_type';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new = [];

            $new = array_merge($new, $this->names($d, 'Text'), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
