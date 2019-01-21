<?php

/**
 * LibraProcessFCReputation
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessFCReputation extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_fc_reputation';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $data = json_decode($d['data'], true);

            $new = [
                /*'text_ja' => $d['Text_ja'],
                'text_en' => $d['Text_en'],
                'text_fr' => $d['Text_fr'],
                'text_de' => $d['Text_de'],

                'color' => $d['Color'],
                'current_point' => $d['CurrentPoint'],
                'next_point' => $d['NextPoint'],
                'rgb' => implode(',', $data['color']),*/
            ];

            $new = array_merge($new, ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
