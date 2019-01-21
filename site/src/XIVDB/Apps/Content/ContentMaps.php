<?php

namespace XIVDB\Apps\Content;

trait ContentMaps
{
    /**
     * Find app data for a piece of content
     *
     * @param $id
     * @param $type
     * @return array
     */
    public function findAppMapData($id, $type)
    {
        $maps = $this->getModule('maps');
        $markers = $maps->getMarkersForContent($type, $id);
        $markers = $maps->handleMarkers($markers);

        if (!$markers) {
            return [];
        }

        // work out some statistics
        $levelMin = 0;
        $levelMax = 0;
        $hpMin = 0;
        $hpMax = 0;
        $hpAvg = [];
        $mpMin = 0;
        $mpMax = 0;
        $mpAvg = [];
        $maps = [];

        // handle entries
        foreach($markers as $i => $entry)
        {
            // add map id
            $mapId = $entry['map_id'];
            if (!isset($maps[$mapId])) {
                $maps[$mapId] = [
                    'placename_id' => $entry['placename_id'],
                    'placename_name' => $entry['placename_name'],
                    'region_id' => $entry['region_id'],
                    'region_name' => $entry['region_name'],
                    'zone_id' => $entry['zone_id'],
                    'zone_name' => $entry['zone_name'],
                    'tooltip' => $entry['tooltip'],
                    'icon' => $entry['icon'],
                ];
            }

            // stats
            if ($entry['app_data']) {
                $level = $entry['app_data']['level'];
                $hp = $entry['app_data']['hp'];
                $mp = $entry['app_data']['mp'];

                $levelMin = ($levelMin == 0 || $level < $levelMin) ? $level : $levelMin;
                $levelMax = ($levelMax == 0 || $level > $levelMax) ? $level : $levelMax;

                $hpMin = ($hpMin == 0 || $hp < $hpMin) ? $hp : $hpMin;
                $hpMax = ($hpMax == 0 || $hp > $hpMax) ? $hp : $hpMax;
                $hpAvg[] = $hp;

                $mpMin = ($mpMin == 0 || $mp < $mpMin) ? $mp : $mpMin;
                $mpMax = ($mpMax == 0 || $mp > $mpMax) ? $mp : $mpMax;
                $mpAvg[] = $mp;
            }
        }

        if ($hpAvg && $mpAvg) {
            $hpAvg = array_sum($hpAvg) / count($hpAvg);
            $mpAvg = array_sum($mpAvg) / count($mpAvg);
        }

        return [
            'stats' => [
                'levelMin' => $levelMin,
                'levelMax' => $levelMax,
                'hpMin' => $hpMin,
                'hpMax' => $hpMax,
                'hpAvg' => $hpAvg,
                'mpMin' => $mpMin,
                'mpMax' => $mpMax,
                'mpAvg' => $mpAvg,
            ],
            'maps' => array_values($maps),
            'points' => $markers,
        ];
    }

    /**
     * This will set:
     * - Enemies
     * - NPCs
     * - Gathering
     *
     * data for the providedplacename id
     *
     * @param $id
     */
    public function setContentFromMapMarkers($placenameId)
    {
        $maps = $this->getModule('maps');
        $markers = $maps->getMarkersForPlacename($placenameId);
        $markers = $maps->handleMarkers($markers);

        if ($markers) {
            foreach($markers as $marker) {
                // if monster
                if ($marker['app_content_type'] == 'Monster') {
                    $content = $this->addEnemy($marker['app_content_id']);
                    if (!$content) {
                        continue;
                    }

                    $this->data['enemies'][] = [
                        'content' => $content,
                        'name' => $marker['content_name'],
                        'position' => $marker['app_position']['ingame'],
                        'icon' => $marker['icon'],
                        'data' => $marker['app_data'],
                    ];
                }

                // if npc
                if ($marker['app_content_type'] == 'NPC') {
                    $content = $this->addNPC($marker['app_content_id']);
                    if (!$content) {
                        continue;
                    }
                    $this->data['npcs'][] = [
                        'content' => $content,
                        'name' => $marker['content_name'],
                        'position' => $marker['app_position']['ingame'],
                        'icon' => $marker['icon'],
                        'data' => [],
                    ];
                }

                // if gathering node
                if ($marker['app_content_type'] == 'Gathering') {
                    $this->data['gathering'][] = [
                        'content' => $marker,
                        'name' => $marker['content_name'],
                        'position' => $marker['app_position']['ingame'],
                        'icon' => $marker['icon'],
                        'data' => [],
                    ];
                }
            }

            $this->cleanEnemiesArray();
            $this->cleanNPCsArray();
            $this->cleanGatheringArray();
        }
    }

    /**
     * Clean up the enemies array
     */
    public function cleanEnemiesArray()
    {
        // reorder data to group up duplicates.
        $temp = [];
        foreach($this->data['enemies'] as $enemy) {
            $id = $enemy['content']['id'];

            if (!isset($temp[$id])) {
                $temp[$id] = [
                    'name' => $enemy['name'],
                    'content' => $enemy['content'],
                    'spawns' => 0,
                    'positions' => [],
                ];
            }

            $temp[$id]['positions'][] = [
                'x' => $enemy['position']['x'],
                'y' => $enemy['position']['y'],
                'icon' => $enemy['icon']['image'],
                'icon_size' => $enemy['icon']['size'],
                'level' => $enemy['data']['level'],
                'hp' => $enemy['data']['hp'],
                'mp' => $enemy['data']['mp'],
            ];

            $temp[$id]['spawns']++;
        }

        $this->sksort($temp, 'name', true);
        $this->data['enemies'] = $temp;
    }

    /**
     * Clean up the NPCs Array
     */
    public function cleanNPCsArray()
    {
        $this->sksort($this->data['npcs'], 'name', true);
    }

    /**
     * Clean up the gathering array
     */
    public function cleanGatheringArray()
    {
        $this->sksort($this->data['gathering'], 'name', true);
    }


    /**
     * @param $data
     * @return mixed
     */
	public function handleMaps($data)
    {
        // if custom position
        if (isset($data['position']['x']))
        {
            $scale = $data['map']['size_factor'];

            $x = $data['position']['x'];
            $y = $data['position']['y'];
            $data['coordinates'] = $data['position'];
            $data['latlong'] = $this->mapGameToPixels($x, $y, $scale);
        }
        elseif (isset($data['xyz']['id']))
        {
            $scale = $data['map']['size_factor'];

            $x = $data['xyz']['x'];
            $y = $data['xyz']['y'];
            $z = $data['xyz']['z'];
            $data['coordinates'] = $this->mapLevelsToGame($x, $z, $scale);


            $x = $data['coordinates']['x'];
            $y = $data['coordinates']['y'];
            $data['latlong'] = $this->mapGameToPixels($x, $y, $scale);
        }

		return $data;
    }

    /**
     * Add urls to maps
     *
     * @param $name
     * @param $maps
     * @return mixed
     */
    public function addMapUrls($name, $maps)
    {
        foreach($maps as $i => $map)
        {
            $name = strtolower(str_ireplace(' ', '+', $name));
            $maps[$i]['url'] = sprintf('/maps#%s/%s', $map['placename'], $name);
        }

        return $maps;
    }

    /**
     * Translate a levels.csv X/Y position + placename
     * maps size_factor into an in game X/Y position
     * Rounded to 1 dp as this is what Lodestone uses
     *
     * @param $x
     * @param $y
     * @param int $mapSizeFactor
     * @return array|bool
     */
	public function mapLevelsToGame($x, $y, $mapSizeFactor = 100)
    {
        if ($x == 0 || $y == 0 || $mapSizeFactor == 0) {
            return false;
        }

        $x = round($this->calcCoordinate($x, $mapSizeFactor), 1);
        $y = round($this->calcCoordinate($y, $mapSizeFactor), 1);

        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    /**
     * levels X/Y to in-game X/Y formula by Clorifex
     * https://github.com/viion/XIV-Datamining/blob/master/research/map_coordinates.md
     *
     * @param $val
     * @param $scale
     * @return float
     */
	private function calcCoordinate($val, $scale)
    {
        $c = $scale / 100;
        $val = $val * $c;
        return ((41.5 / $c) * (($val + 1024.0) / 2048.0)) + 1;
    }

    /**
     * Translates an in-game X/Y position to an levels valid X/Y position
     * This is the opposite of mapLevelsToGame
     *
     * @param $x
     * @param $y
     * @param $mapSizeFactor
     * @return array
     */
	public function mapGameToLevels($x, $y, $mapSizeFactor)
    {
        $mapSizeFactor = $mapSizeFactor / 100;

        $x = ($x*50)-25-(1024 / $mapSizeFactor);
        $y = ($y*50)-25-(1024 / $mapSizeFactor);

        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    /**
     * Converts game coordinates to pixels
     *
     * @param $x
     * @param $y
     * @param int $mapSizeFactor
     * @return array
     */
	public function mapGameToPixels($x, $y, $mapSizeFactor = 100)
	{
		if ($mapSizeFactor <= 100) {
			$diff = (100 - $mapSizeFactor) / 100 + 1;
			$max = 44 * $diff;
		} else {
			$diff = $mapSizeFactor / 100;
			$max = 44 / $diff;
		}

		$xDistance = 4.1 * ($x / $max);
		$yDistance = -(4.1 * ($y / $max));

		return [
			'x' => round($xDistance, 4),
			'y' => round($yDistance, 4),
		];
	}
}
