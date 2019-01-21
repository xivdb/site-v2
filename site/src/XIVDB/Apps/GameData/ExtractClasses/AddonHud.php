<?php

/**
 * AddonHud
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class AddonHud extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_addon_hud';

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'command' => strtolower($line['command']['en']),
            'command_short' => strtolower($line['command_short']['en']),
        ];
    }
}
