<?php

/**
 * ContentType
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ContentType extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_contents_type';

    protected $real =
    [
        2 => 'icon',
        3 => 'icon_mini',
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
}
