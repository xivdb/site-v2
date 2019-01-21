<?php

/**
 * AddonSlotUi
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class AddonSlotUi extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_items_ui_slot';

    protected function json($line)
    {
        if ($id < 738 || $id > 754) {
            return false;
        }

        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),
        ];
    }
}
