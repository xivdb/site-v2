<?php

/**
 * Map
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Map extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_placenames_maps';

    protected $real =
    [
        4 => 'hierarchy',
        5 => 'map_marker_range',
        6 => 'folder',
        7 => 'size_factor',
        8 => 'offset_x',
        9 => 'offset_y',
        10 => 'region',
        11 => 'placename',
        12 => 'zone',
        15 => 'territory_id',
    ];

    protected function manual()
    {
        $this->updatePlaceNamesRegionId();
        $this->updateMapFolderPaths();
    }

    //
    // Update region id within place names.
    //
    private function updatePlaceNamesRegionId()
    {
        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
            ->reset()
            ->select('id, placename, region')
            ->from('xiv_placenames_maps');

        $maps = $dbs->get()->all();

        foreach($maps as $map) {
            $dbs->QueryBuilder
                ->reset()
                ->update('xiv_placenames')
                ->set(['region' => $map['region']])
                ->where('id = :id')
                ->bind(':id', $map['placename']);

            $dbs->execute();
        }
    }

    //
    // Update the folder maps in placenames maps
    //
    private function updateMapFolderPaths()
    {
        $folderOffset = array_flip($this->real)['folder'];

        $dbs = $this->getModule('database');

        foreach($this->getCsvFileData() as $id => $line)
        {
            $folder = $line[$folderOffset];
            if (!$folder) {
                continue;
            }

            $folder = explode('/', $folder);
            $path = sprintf('%s/%s.%s.png', $folder[0], $folder[0], $folder[1]);

            $dbs->QueryBuilder
                ->reset()
                ->update('xiv_placenames_maps')
                ->set('path', ':path')
                ->bind(':path', $path)
                ->where('id = :id')
                ->bind(':id', $id);

            $dbs->execute();
        }
    }
}
