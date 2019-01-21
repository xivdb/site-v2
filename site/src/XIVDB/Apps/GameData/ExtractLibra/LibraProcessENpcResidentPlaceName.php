<?php

/**
 * LibraProcessENpcResidentPlaceName
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessENpcResidentPlaceName extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_npc_to_region';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $new = [
                'npc' => $d['ENpcResident_Key'],
                'placename' => $d['PlaceName_Key'],
                'region' => $d['region'],
            ];

            $new = array_merge($new, ['patch' => $this->patch]);
            $insert[] = $new;
        }

        return $this->insert($table, $insert);
    }
}
