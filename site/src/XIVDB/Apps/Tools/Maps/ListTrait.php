<?php

namespace XIVDB\Apps\Tools\Maps;

/**
 * Class ListTrait
 * @package XIVDB\Apps\Tools\Maps
 *
 * Functions for getting list of stuff
 */
trait ListTrait
{
    /**
     * @return array
     */
    public function getList()
    {
        $dbs = $this->getModule('database');

        // vars
        $maps = [];
        $regions = [];
        $regionsToPlacename = [];
        $global = [];

        //  get all maps
        $dbs->QueryBuilder->select('*')->from('xiv_placenames_maps');
        $placenamesMaps = $dbs->get()->all();

        // get all placenames
        $dbs->QueryBuilder->select(['id', 'name_{lang} as name', 'region'])->from('xiv_placenames');
        $placenames = $this->restructure($dbs->get()->all(), 'id');

        // array placenames into regions
        foreach($placenames as $id => $place) {
           $regionId = $place['region'];
           if ($regionId > 0) {
               $regions[$regionId] = $placenames[$regionId];
               $regionsToPlacename[$id] = $regionId;
           } else {
               $global[] = $place;
           }
        }

        // restructure global
        $global = $this->restructure($global, 'id');

        // sort map array
        foreach($placenamesMaps as $map)
        {
           // if no folder or folder is default
           if (empty($map['folder']) || stripos($map['folder'], 'default') !== false) {
               continue;
           }

           // placename id
           $placenameId = $map['placename'];

           // get placename, if non, continue!
           $placenameData = isset($placenames[$placenameId]) ? $placenames[$placenameId] : null;
           if (!$placenameData) {
               continue;
           }

           $placenameData['url'] = $this->url('placename', $placenameId, $placenameData['name']);

           // get id and layer
           $folderData = explode('/', $map['folder']);
           $folder = $folderData[0];
           $layer = $folderData[1];

           // region stuff
           $region = isset($regionsToPlacename[$placenameId]) ? $regions[$regionsToPlacename[$placenameId]] : null;
           $regionId = isset($region['id']) ? $region['id'] : '0';

           // create region
           if (!isset($maps[$regionId]))
           {
               $region['name'] = isset($region['name']) ? $region['name'] : 'Eorzea';
               $region['placenames'] = [];
               $maps[$regionId] = $region;
           }

           // create placename
           if (!isset($maps[$regionId]['placenames'][$placenameId]))
           {
               $maps[$regionId]['placenames'][$placenameId] =
               [
                   'id' => $placenameId,
                   'name' => $placenameData['name'],
                   'url' => $placenameData['url'],
                   'folder' => $folder,
                   'folder_layer' => $layer,
                   'size_factor' => $map['size_factor'],
                   'territory_id' => $map['territory_id'],
                   'path' => '/img/maps/'. $map['path'],
                   'layers' => [],
               ];
           }

           // append layer
           $layer = sprintf('/img/maps/%s/%s.%s.jpg', $folder, $folder, $layer);
           $maps[$regionId]['placenames'][$placenameId]['layers'][] = $layer;
        }

        // sort placenames by name for each region
        foreach($maps as $i => $map) {
           $this->sksort($map['placenames'], 'name', true);
           $maps[$i] = $map;
        }

        // sort entire array A-Z
        $this->ksortRecursive($maps);
        $maps = array_values($maps);
        return $maps;
    }

    /**
     * Get a list of maps
     * @return mixed
     */
    public function getMapList()
    {
        //  get all maps
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->select('*')->from('xiv_placenames_maps');
        return $dbs->get()->all();
    }

    /**
     * GEt a list of maps that are dungeons
     *
     * @return mixed
     */
    public function getDungeonMapList()
    {
        //  get all maps
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                'xiv_placenames_maps' => [
                    'id as map_id',
                    'region as map_region',
                    'placename as map_placename',
                    'territory_id as map_territory_id',
                    'path as map_file',
                ],
                'xiv_instances' => [
                    'id as instance_id',
                    'name_{lang} as instance_name',
                ]
            ])
            ->from('xiv_placenames_maps')
            ->join(['xiv_placenames_maps' => 'placename'], ['xiv_instances' => 'placename']);

        return $dbs->get()->all();
    }
}