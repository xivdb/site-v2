<?php

/**
 * LibraProcessGathering
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessGathering extends CoreLibra
{
    protected function handle()
    {
        $table = 'xiv_gathering';

        $insert = [];
        $nodes = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new = [
                /*'item' => $d['Item'],
                'gathering_type' => $d['GatheringType'],
                'level' => $d['Level'],
                'level_view' => $d['levelView'],
                'level_diff' => $d['levelDiff'],
                'is_hidden' => $d['is_hidden'],
                'gathering_notebook_list' => $d['GatheringNotebookList'],
                'gathering_item_number' => $d['GatheringItemNo'],*/
                'lodestone_type' => $lodestone[0],
                'lodestone_id' => $lodestone[1],
            ];

            $new = array_merge($new, $this->names($d, 'Index'), ['id' => $id, 'patch' => $this->patch]);
            $insert[] = $new;

            if (isset($json['GatheringPoints']))
            {
                foreach($json['GatheringPoints'] as $regiondata)
                {
                    $regionId = $regiondata['key'];
                    $zones = $regiondata['children'];

                    foreach($zones as $zonedata)
                    {
                        $zoneId = $zonedata['key'];
                        $placenames = $zonedata['children'];

                        foreach($placenames as $placedata)
                        {
                            $placenameId = $placedata['key'];
                            $level = $placedata['level'];

                            $nodes[] = [
                                'gathering' => $id,
                                'region' => $regionId,
                                'zone' => $zoneId,
                                'placename' => $placenameId,
                                'level' => $level,
                                'patch' => $this->patch
                            ];

                            if (count($nodes) == 500)
                            {
                                $this->insert('xiv_gathering_to_nodes', $nodes);
                                $nodes = [];
                            }
                        }
                    }
                }
            }
        }

        $this->insert($table, $insert);
        $this->insert('xiv_gathering_to_nodes', $nodes);

        return true;
    }
}
