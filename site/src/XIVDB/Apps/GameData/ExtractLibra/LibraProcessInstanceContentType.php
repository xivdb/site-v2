<?php

/**
 * LibraProcessInstanceContentType
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessInstanceContentType extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_instances_type';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $new = [
                'id' => $d['Type'],
                'sort' => $d['Sortkey'],
                'content_type' => $d['ContentType']
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
