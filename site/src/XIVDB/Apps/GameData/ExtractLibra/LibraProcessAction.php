<?php

/**
 * LibraProcessAction
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessAction extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_actions';

        $insert = [];
        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];

            $new =
            [
                'icon' => $d['Icon'],
                'help_ja' => $d['HelpWeb_ja'],
                'help_en' => $d['HelpWeb_en'],
                'help_fr' => $d['HelpWeb_fr'],
                'help_de' => $d['HelpWeb_de'],
                'help_cns' => null,
            ];

            $new = array_merge($new, $this->names($d), ['id' => $id, 'patch' => $this->patch]);
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
