<?php

namespace XIVDB\Apps\Tools\Mapper;

use Symfony\Component\HttpFoundation\Request;

class Mapper extends \XIVDB\Apps\AppHandler
{
	use Dashboard;
	use Placenames;
	use Saver;

	private $data;
	public $count = 0;

    /**
     * @param $data
     * @return $this
     */
	public function setData($data)
	{
        $this->count = 0;
		$this->data = json_decode($data, true);
		return $this;
	}

    /**
     * Process map import
     */
	public function process()
	{
		// loop through submissions
		foreach($this->data as $entry)
		{
			// if entry part of ignore list
			if ($this->ignore($entry)) {
				continue;
			}

			// get map
			$mapId = $entry['map'];
			$map = $this->getPlacenameFromMapId($mapId);
            if (!$map || !$map['placename']) {
                continue;
            }

			// setup position
			$positions = [
				'map' => $map,
				'position' => [
					'x' => round($entry['x'], 1),
					'y' => round($entry['y'], 1),
					'z' => round($entry['z'], 1),
				],
				'ingame' => [
					'x' => round($entry['real_x'], 1),
					'y' => round($entry['real_y'], 1),
				],
				'coordinates' => [
					'x' => $this->convertIngamePositionCoordinate(round($entry['x'], 1), $map['size_factor'], $map['offset_x']),
					'y' => $this->convertIngamePositionCoordinate(round($entry['y'], 1), $map['size_factor'], $map['offset_y']),
				]
			];

            // round everything!!!
            round($positions['position']['x'], 1);
            round($positions['position']['y'], 1);
            round($positions['position']['z'], 1);
            round($positions['ingame']['x'], 1);
            round($positions['ingame']['y'], 1);
            round($positions['coordinates']['x'], 1);
            round($positions['coordinates']['y'], 1);

			// setup data
			$data = [];
			if (isset($entry['level'])) {
				$data = [
					'level' => $entry['level'],
					'hp' => $entry['hp'],
					'mp' => $entry['mp'],
				];
			}

			if (isset($entry['fate'])) {
				$data['fate'] = [
					'fate' => $entry['fate'],
					'is_fate' => $entry['is_fate'],
				];
			}

			// generate a new hash
			$hash = $this->generateHash($entry);

			// save
			$this->save($hash, $entry, $positions, $data, $map);
			$this->count++;
		}
	}

    /**
     * @param $entry
     */
	public function generateHash($entry)
    {
        $x = round($entry['real_x'], 1);
        $y = round($entry['real_y'], 1);

        // round decimal to an even number
        $xDecimal = explode('.', $x);
        $xDecimal = isset($xDecimal[1]) ? $xDecimal : 0;
        if ($xDecimal % 3 == 1) {
            $xDecimal++;
        }

        $yDecimal = explode('.', $y);
        $yDecimal = isset($yDecimal[1]) ? $yDecimal : 0;
        if ($yDecimal % 3 == 1) {
            $yDecimal++;
        }

        // generate a hash to reduce duplicates
        $hash = [$entry['map'], $entry['id'], $x, $y];

        $hash = sha1(implode('', $hash));
        return $hash;
    }

    /**
     * @param $entry
     * @return bool
     */
	public function ignore($entry)
	{
		$monsterIgnoreIds =
		[
			'1404', // garuda-egit
			'1398', // Eos
		];

		// if monster and in monster ignore list
		if ($entry['type'] == 'Monster' && in_array($entry['id'], $monsterIgnoreIds)) {
			return true;
		}

        // don't include stuff thats obviously out of range
        if (
            $entry['real_x'] > 100
            || $entry['real_y'] > 100
            || $entry['real_x'] == 0
            || $entry['real_y'] == 0
            || $entry['real_x'] < 0
            || $entry['real_y'] < 0
        ) {
            return true;
        }
		return false;
	}
}
