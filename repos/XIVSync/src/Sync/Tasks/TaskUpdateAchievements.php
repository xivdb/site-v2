<?php

namespace Sync\Tasks;

//
// Update achievements
//
class TaskUpdateAchievements
{
	private $Achievements;

	function __construct()
	{
		$this->Achievements = new \Sync\App\Achievements();
	}

	public function init($start)
	{
		$list = $this->Achievements->getLastUpdatedAchievements($start);
		if (!$list) {
            output('No characters to update! (Weird!)');
			return;
		}

		$completed = $this->runAction($list);
		$timePassed = time() - START_TIME;

        output('Updated %s characters achievements in %s seconds.', [ $completed, $timePassed ]);
	}

	//
	// Loop through all characters
	//
	private function runAction($list)
	{
		$complete = 0;
		foreach($list as $character)
		{
			// get time remaining
			$timePassed = time() - START_TIME;
			if ($timePassed >= TIME_LIMIT) {
				output('Breaking due to %s seconds passed', [ $timePassed ]);
				break;
			}

			$achievements = [];
			$achievementsPrivate = false;
			$totalPointsObtainedReborn = 0;
			$totalPointsPossibleReborn = 0;
			$totalPointsObtainedLegacy = 0;
			$totalPointsPossibleLegacy = 0;
			$totalPossibleAchievements = [];
			$achievementLastTime = 0;

			// loop through each category
			foreach([1, 2, 3, 4, 5, 6, 8, 11, 12] as $kind)
			{
				// get character data (also parses it)
				$url = sprintf(LODESTONE_ACHIEVEMENTS_URL, $character['lodestone_id'], $kind);
				$data = $this->Achievements->requestFromLodestone($url);
				if (!$data) {
                    $achievementsPrivate = true;
                    output('Achievements not found, assuming private');
					break;
				}

				// if private
                if ($data == 'private') {
                    $achievementsPrivate = true;
					break;
				}

				foreach($data['list'] as $achieve) {
					if (!$achieve['timestamp']) {
						continue;
					}

					$time = strtotime($achieve['timestamp']);

					if ($time > $achievementLastTime) {
						$achievementLastTime = $time;
					}
				}

				$achievements[$kind] = $data['list'];
				$totalPointsObtainedReborn += $data['points']['obtained'];
				$totalPointsPossibleReborn += $data['points']['possible'];
				$totalPossibleAchievements = array_merge(
					$totalPossibleAchievements,
					$data['list_possible']
				);
			}

			// if achievements not private, try legacy category and save stuff
			if (!$achievementsPrivate) {
				// try legacy category
				$url = sprintf(LODESTONE_ACHIEVEMENTS_URL, $character['lodestone_id'], 13);
				$data = $this->Achievements->requestFromLodestone($url);
				if ($data) {
					$achievements[13] = $data['list'];
					$totalPointsObtainedLegacy += $data['points']['obtained'];
					$totalPointsPossibleLegacy += $data['points']['possible'];
					$totalPossibleAchievements = array_merge(
						$totalPossibleAchievements,
						$data['list_possible']
					);

					foreach($data['list'] as $achieve) {
						if (!$achieve['timestamp']) {
							continue;
						}

						$time = strtotime($achieve['timestamp']);

						if ($time > $achievementLastTime) {
							$achievementLastTime = $time;
						}
					}
				}

				// Insert/Update players achievement list
				$this->Achievements->addAchievements($character, $achievements);

				// Insert/Update players achievements possible
				$this->Achievements->addPossibleAchievements($character, $totalPossibleAchievements);

				// Update a players points and achievement dates
				$this->Achievements->updateCharacter($character, [
					'totalPointsObtainedReborn' => $totalPointsObtainedReborn,
					'totalPointsPossibleReborn' => $totalPointsPossibleReborn,
					'totalPointsObtainedLegacy' => $totalPointsObtainedLegacy,
					'totalPointsPossibleLegacy' => $totalPointsPossibleLegacy,
					'achievementLastTime' => $achievementLastTime,
				]);
			} else {
				// achievements private, set status
				$this->Achievements->setPrivate($character);
                output('Characters achievements are private.');
			}

			$complete++;
		}

		return $complete;
	}
}
