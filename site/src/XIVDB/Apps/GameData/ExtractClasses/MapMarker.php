<?php

/**
 * MapMarker
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class MapMarker extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_maps_marker';

    protected $real =
    [
        2 => 'x',
        4 => 'icon',
        9 => 'placename',
    ];
}
