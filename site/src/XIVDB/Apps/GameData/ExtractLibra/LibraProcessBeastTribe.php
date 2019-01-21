<?php

/**
 * LibraProcessBeastTribe
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessBeastTribe extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_beast_tribe';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new =
            [
                'reputation_value_ja' => $d['ReputationValue_ja'],
                'reputation_value_en' => $d['ReputationValue_en'],
                'reputation_value_fr' => $d['ReputationValue_fr'],
                'reputation_value_de' => $d['ReputationValue_de'],
            ];

            $new = array_merge($new, $this->names($d, 'SGL'), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }
        }

        return $this->insert($table, $insert);
    }
}
