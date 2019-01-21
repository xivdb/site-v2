<?php

/**
 * Addon
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Addon extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_addons';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),
        ];
    }

    public function manual()
    {
        $this->AddonSlotUi();
    }

    private function AddonSlotUi()
    {
        if ($json = $this->getJsonFileData())
        {
            $insert = [];
            foreach($json as $id => $line)
            {
                if ($id < 738 || $id > 754) {
                    continue;
                }

                $insert[] = [
                    'id' => $id,
                    'name_ja' => $line['name']['ja'],
                    'name_en' => ucwords($line['name']['en']),
                    'name_fr' => ucwords($line['name']['fr']),
                    'name_de' => ucwords($line['name']['de']),
                ];
            }

            $this->insert('xiv_items_ui_slot', $insert);
        }
    }
}
