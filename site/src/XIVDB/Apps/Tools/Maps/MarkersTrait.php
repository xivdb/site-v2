<?php

namespace XIVDB\Apps\Tools\Maps;

use XIVDB\Apps\Content\ContentDB;

/**
 * Class ListTrait
 * @package XIVDB\Apps\Tools\Maps
 *
 * Functions for getting markers from maps
 */
trait MarkersTrait
{
    /**
     * Get submitted map data,
     * - Include placename
     * - Include region
     * - Include zone
     * - Include map entry
     */
    private function getQuery()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                ContentDB::APP_MAPPER => [
                    'hash', 'added as app_added', 'position as app_position', 'ingame_xy as app_xy',
                    'data as app_data', 'content_type as app_content_type', 'id as app_content_id',
                    'added as app_added', 'name as content_name'
                ],
                ContentDB::PLACENAMES => [
                    'id as placename_id', 'name_{lang} as placename_name'
                ],
                'region' => [
                    'id as region_id', 'name_{lang} as region_name'
                ],
                'zone' => [
                    'id as zone_id', 'name_{lang} as zone_name'
                ],
                ContentDB::PLACENAMES_MAPS => [
                    'id as map_id', 'folder as map_folder', 'path as map_path', 'size_factor as map_size_factor',
                    'offset_x as map_offset_x', 'offset_y as map_offset_y',
                ],
            ])
            ->from(ContentDB::APP_MAPPER)
            ->join([ ContentDB::APP_MAPPER => 'placename' ], [ ContentDB::PLACENAMES => 'id'])
            ->join([ ContentDB::APP_MAPPER => 'map' ], [ ContentDB::PLACENAMES_MAPS => 'id'])
            ->join([ ContentDB::PLACENAMES_MAPS => 'region' ], [ ContentDB::PLACENAMES => 'id'], 'region')
            ->join([ ContentDB::PLACENAMES_MAPS => 'zone' ], [ ContentDB::PLACENAMES => 'id'], 'zone')
            ->limit(0,10000);

        return $dbs;
    }

    /**
     * Handle markers
     *
     * @param $markers
     * @return mixed
     */
    public function handleMarkers($markers)
    {
        $icons = [
            'Monster' => [
                1 => [
                    'image' => '/img/game_map_icons/enemy_level_1.png',
                    'size' => 16,
                ],
                2 => [
                    'image' => '/img/game_map_icons/enemy_level_2.png',
                    'size' => 16,
                ],
                3 => [
                    'image' => '/img/game_map_icons/enemy_level_3.png',
                    'size' => 16,
                ],
                4 => [
                    'image' => '/img/game_map_icons/enemy_level_4.png',
                    'size' => 16,
                ],
                5 => [
                    'image' => '/img/game_map_icons/enemy_level_5.png',
                    'size' => 16,
                ],
                6 => [
                    'image' => '/img/game_map_icons/enemy_level_6.png',
                    'size' => 16,
                ],
            ],
            'NPC' => [
                'image' => '/img/game_map_icons/npc.png',
                'size' => 24,
            ],
            'Aetheryte' => [
                'image' => '/img/game_map_icons/aether.png',
                'size' => 20,
            ],
            'Gathering' => [
                'image' => '/img/game_map_icons/gathering.png',
                'size' => 16,
            ],
            'EObj' => [
                'image' => '/img/game_map_icons/default.png',
                'size' => 20,
            ],
        ];

        $gatheringIcons = [
            'Lush Vegetation Patch' => '/img/game_map_icons/gathering_rare_harvesting.png',
            'Mature Tree' => '/img/game_map_icons/gathering_logging.png',
            'Mineral Deposit' => '/img/game_map_icons/gathering_mining.png',
            'Rocky Outcrop' => '/img/game_map_icons/gathering_quarrying.png',

            'Concealed Lush Vegetation Patch' => '/img/game_map_icons/gathering_rare_harvesting.png',
            'Concealed Mature Tree' => '/img/game_map_icons/gathering_rare_logging.png',
            'Concealed Mineral Deposit' => '/img/game_map_icons/gathering_rare_mining.png',
            'Concealed Rocky Outcrop' => '/img/game_map_icons/gathering_rare_quarrying.png',

            'Ephemeral Lush Vegetation Patch' => '/img/game_map_icons/gathering_rare_harvesting.png',
            'Ephemeral Mature Tree' => '/img/game_map_icons/gathering_rare_logging.png',
            'Ephemeral Mineral Deposit' => '/img/game_map_icons/gathering_rare_mining.png',
            'Ephemeral Rocky Outcrop' => '/img/game_map_icons/gathering_rare_quarrying.png',

            'Legendary Lush Vegetation Patch' => '/img/game_map_icons/gathering_rare_harvesting.png',
            'Legendary Mature Tree' => '/img/game_map_icons/gathering_rare_logging.png',
            'Legendary Mineral Deposit' => '/img/game_map_icons/gathering_rare_mining.png',
            'Legendary Rocky Outcrop' => '/img/game_map_icons/gathering_rare_quarrying.png',

            'Unspoiled Lush Vegetation Patch' => '/img/game_map_icons/gathering_rare_harvesting.png',
            'Unspoiled Mature Tree' => '/img/game_map_icons/gathering_rare_logging.png',
            'Unspoiled Mineral Deposit' => '/img/game_map_icons/gathering_rare_mining.png',
            'Unspoiled Rocky Outcrop' => '/img/game_map_icons/gathering_rare_quarrying.png',
        ];

        $ignoreList = ['Aetheryte', 'EObj'];
        $defaultIcon = [
            'image' => '/img/game_map_icons/default.png',
            'size' => 32,
        ];

        // handle entries
        foreach($markers as $i => $entry) {
            // remove any ignored markers
            if (in_array($entry['app_content_type'], $ignoreList)) {
                unset($markers[$i]);
                continue;
            }

            // decode json data
            $appPos = json_decode($entry['app_position'], true);
            $appData = json_decode($entry['app_data'], true);

            // remove useless stuff
            unset($appPos['map']);

            // set data
            $type = $entry['app_content_type'];
            $name = $entry['content_name'];
            $level = isset($appData['level']) ? $appData['level'] : 0;

            // if no name, ignore it
            if (!$name) {
                unset($markers[$i]);
                continue;
            }

            // set some marker data
            $markers[$i]['app_position'] = $appPos;
            $markers[$i]['app_data'] = $appData;
            $markers[$i]['icon'] = $defaultIcon;

            // handle icon based on type
            if (isset($icons[$type])) {
                $icon = $icons[$type];

                if ($type == 'Monster') {
                    if ($level < 10) {
                        $icon = $icon[1];
                    } else if ($level < 20) {
                        $icon = $icon[2];
                    } else if ($level < 30) {
                        $icon = $icon[3];
                    } else if ($level < 40) {
                        $icon = $icon[4];
                    } else if ($level < 50) {
                        $icon = $icon[5];
                    } else {
                        $icon = $icon[6];
                    }
                }

                if ($type == 'Gathering') {
                    if (isset($gatheringIcons[$name])) {
                        $icon['image'] = $gatheringIcons[$name];
                    }
                }

                $markers[$i]['icon'] = $icon;
            }

            // generate tooltip
            $tooltip = sprintf('%s (X: %s - Y: %s)', $name, $appPos['ingame']['x'], $appPos['ingame']['y']);
            if ($level > 1) {
                $tooltip = sprintf('Lv.%s %s', $level, $tooltip);
            }

            $tooltip = sprintf('[%s] %s', $type, $tooltip);
            $markers[$i]['tooltip'] = $tooltip;
        }

        //show($markers);

        return array_values($markers);
    }

    /**
     * Get markers for a specific piece of content
     *
     * @param $id
     * @param $type
     * @return mixed
     */
    public function getMarkersForContent($id, $type)
    {
        $dbs = $this->getQuery();
        $dbs->QueryBuilder
            ->where(ContentDB::APP_MAPPER .'.content_type = :type')->bind('type', $type)
            ->where(ContentDB::APP_MAPPER .'.id = :id')->bind('id', $id);

        return $dbs->get()->all();
    }

    /**
     * Get markers for a map
     *
     * @param $id
     * @return mixed
     */
    public function getMarkersForMap($id)
    {
        $dbs = $this->getQuery();
        $dbs->QueryBuilder
            ->where(ContentDB::APP_MAPPER .'.map = :id')
            ->bind('id', $id);

        return $dbs->get()->all();
    }

    /**
     * Get markers for a placename
     *
     * @param $id
     * @return mixed
     */
    public function getMarkersForPlacename($id)
    {
        $dbs = $this->getQuery();
        $dbs->QueryBuilder
            ->where(ContentDB::APP_MAPPER .'.placename = :id')
            ->bind('id', $id);

        return $dbs->get()->all();
    }
}