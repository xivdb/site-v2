<?php

/**
 * Stain
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Stain extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_stains';

    protected $real =
    [
        1 => 'color',
        2 => 'shade',
    ];

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

    protected function manual()
    {
        $this->setColorCodes();
    }

    //
    // Set the correct rgba color code
    //
    private function setColorCodes()
    {
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $offset = $this->getOffset('color');
            $color = $line[$offset];
            $color = dechex($color);

            $insert[] = [
                'id' => $id,
                'color_hex' => '#'. $color,
            ];
        }

        $this->insert(self::TABLE, $insert);
    }
}
