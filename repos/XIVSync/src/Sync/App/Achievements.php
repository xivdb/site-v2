<?php

namespace Sync\App;

//
// Achievements class (db actions)
//
class Achievements
{
	private $dbs;
	private $http;
	private $parser;

	function __construct()
	{
		$this->dbs = new \Sync\Modules\Database();
		$this->http = new \Sync\Modules\HttpRequest();
		$this->parser = new \Sync\Parser\Achievements();
	}

	//
	// Request achievements data from lodestone
	//
	public function requestFromLodestone($url)
	{
		$html = $this->http->get($url);
		if (!$html) {
			return false;
		}

		$data = $this->parser->parse($html);
		if (!$data) {
			return false;
		}

		return $data;
	}


	//
	// Get the last updated achievements from the database
	//
	public function getLastUpdatedAchievements($start)
	{
		$this->dbs->QueryBuilder
			->select([ 'lodestone_id', 'achievements_last_changed', 'achievements_score_reborn' ], false)
			->from('characters')
			->order('achievements_last_updated', 'asc');

		// if start is above 9, it scans private
		// else it scans only public
		if ($start >= 20) {
			// achievements are private, check to see if they're public
			$start = $start - 20;
			$this->dbs->QueryBuilder->where('achievements_public = 0')->limit(($start * AUTO_UPDATE_MAX), AUTO_UPDATE_MAX);
		} else {
			// update those who have public achievements
			$this->dbs->QueryBuilder->where('achievements_public = 1')->limit(($start * AUTO_ACHIEVEMENT_MAX), AUTO_ACHIEVEMENT_MAX);
		}

		return $this->dbs->get()->all();
	}

	//
	// Add achievements
	//
	public function addAchievements($character, $list)
	{
		$this->dbs->QueryBuilder
			->insert('characters_achievements_list')
			->schema([ 'lodestone_id', 'achievement_id', 'obtained' ])
			->duplicate([ 'lodestone_id' ]);

		foreach($list as $kind => $achievements) {
			foreach($achievements as $achi) {
				// only add achievements obtained
				if ($achi['timestamp']) {
					$this->dbs->QueryBuilder->values([
						$character['lodestone_id'],
						$achi['id'],
						$achi['timestamp']
					], true);
				}
			}
		}

		$this->dbs->execute();
	}

	//
	// Add possible achievements
	//
	public function addPossibleAchievements($character, $list)
	{
		$list = json_encode($list);

		$this->dbs->QueryBuilder
			->insert('characters_achievements_possible')
			->schema([ 'lodestone_id', 'last_updated', 'possible' ])
			->values([ $character['lodestone_id'], timestamp(), $list ])
			->duplicate([ 'lodestone_id' ]);

		$this->dbs->execute();
	}

	//
	// Update a players points
	//
	public function updateCharacter($character, $points)
	{
		// if the achievement points are different, update the last change time
		$lastChanged = strtotime($character['achievements_last_changed']);

		// set timestamp
		if ($points['achievementLastTime'] > $lastChanged) {
			$lastChanged = date('Y-m-d H:i:s', $points['achievementLastTime']);
		} else {
		    $lastChanged = date('Y-m-d H:i:s', $lastChanged);
        }

		// update character
		$this->dbs->QueryBuilder
			->update('characters')
			->set([
				'achievements_last_updated' => ':last_updated',
				'achievements_last_changed' => ':last_changed',
				'achievements_public' => 1,
				'achievements_score_reborn' => $points['totalPointsObtainedReborn'],
				'achievements_score_legacy' => $points['totalPointsObtainedLegacy'],
				'achievements_score_reborn_total' => $points['totalPointsPossibleReborn'],
				'achievements_score_legacy_total' => $points['totalPointsPossibleLegacy'],
			])
			->bind('last_updated', timestamp())
			->bind('last_changed', $lastChanged)
			->where('lodestone_id = '. $character['lodestone_id']);

		$this->dbs->execute();
	}

	//
	// Set achievements as public
	//
	public function setPublic($character)
	{
		// update character
		$this->dbs->QueryBuilder
			->update('characters')
			->set([
				'achievements_public' => 1,
				'achievements_last_updated' => ':last_updated',
			])
			->bind('last_updated', timestamp())
			->where('lodestone_id = '. $character['lodestone_id']);

		$this->dbs->execute();
	}

	//
	// Set achievements as private
	//
	public function setPrivate($character)
	{
		// update character
		$this->dbs->QueryBuilder
			->update('characters')
			->set([
				'achievements_public' => 0,
				'achievements_last_updated' => ':last_updated',
			])
			->bind('last_updated', timestamp())
			->where('lodestone_id = '. $character['lodestone_id']);

		$this->dbs->execute();
	}
}
