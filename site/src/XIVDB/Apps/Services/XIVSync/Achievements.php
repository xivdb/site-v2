<?php

namespace XIVDB\Apps\Services\XIVSync;

trait Achievements
{
	//
	// get an achievements census
	//
	public function getAchievementsCensus($achievementId)
	{
		if (!is_numeric($achievementId)) {
			return false;
		}

		// api route
		$route = '/achievements/get/%s/census';
		$route = sprintf($route, $achievementId);

		/// get data
		$data = $this->api($route);
		if (!$data) {
			return false;
		}

		return $data;
	}
}
