<?php

namespace XIVDB\Apps\Characters;

trait DataStatistics
{
	private $trackingStart = null;
	private $trackingFinished = null;
	private $expPerHourDuration = 3;

	protected function handleStatistics()
	{
		$this->tempdata['stats_summary'] = [];

		$this->add('per_hour_average', $this->expPerHourDuration);

		$this->handleProgressToAllMaxed();
		$this->handleBestExpAndLevelsEvent();
		$this->handleBestExpAndLevelsEventPerClass();
		$this->handleExpGainedGraphData();
		$this->handleTrackingLegend();
		$this->handleTrackingDuration();
		$this->handleExpStatistics();
		$this->handleLevelAchievements();
		$this->handlePrediction();
	}

	private function add($key, $data)
	{
		$this->tempdata['stats_summary'][$key] = $data;
	}

	//
	// Work out the prediction for level 60 on each class.
	//
	private function handlePrediction()
	{
		$totalPredictionSessions = 0;
		$totalPredictionClasses = 0;

		foreach($this->chardata['data']['classjobs'] as $id => $role) {
			// classjob exp stats key
			$key = 'exp_stats_'. $id;
			$keySave = 'prediction_'. $id;

			// check for summary data
			if (isset($this->tempdata['stats_summary'][$key])) {
				// get exp per session
				$expSession = $this->tempdata['stats_summary']['exp_stats_'. $id]['exp_session'];

				if ($expSession > 1) {
					$expTogo = $role['exp']['total_togo'];
					$sessionsTogo = ceil($expTogo / $expSession);

					$datetime = new \DateTime();
					$datetime->modify(sprintf('+%s day', $sessionsTogo));

					$this->add($keySave, [
						'dt' => $datetime,
						'days' => $sessionsTogo,
						'unix' => '@'. $datetime->format('U'),
					]);

					// increment totals
					$totalPredictionSessions = $totalPredictionSessions + $sessionsTogo;
					$totalPredictionClasses = $totalPredictionClasses + 1;
				}
			}
		}

		//
		// TODO:
		//
		// Work out how many classes need to hit lv 60
		// Work out the average EXP per class already done
		//
		// Use that average to work out a session amount for all classes.
		//

		/*
		if ($totalPredictionClasses > 1) {
			$this->add('prediction', [
				'dt' => $datetime,
				'days' => $totalPredictionSessions,
				'classes' => $totalPredictionClasses,
				'unix' => '@'. $datetime->format('U'),
			]);
		}
		*/
	}

	//
	// Get the achievements for each level 60 class
	//
	private function handleLevelAchievements()
	{
		$classJobIds = [
			1 => 1167, // gladiator
			2 => 1168, // pugilist
			3 => 1169, // marauder
			4 => 1170, // lancer
			5 => 1171, // archer
			29 => 1175, // rogue
			32 => 1179, // dark knight
			31 => 1183, // machanist

			6 => 1172, // conjurer
			7 => 1173, // thaum
			26 => 1174, // arcanist
			33 => 1187, // astrologian

			8 => 1265, // carpenter
			9 => 1266, // blacksmith
			10 => 1267, // armorer
			11 => 1268, // goldsmith
			12 => 1269, // leatherworker
			13 => 1270, // weaver
			14 => 1271, // alchemist
			15 => 1272, // culinarian

			16 => 1273, // miner
			17 => 1274, // botany
			18 => 1275, // fisher
		];

		$achieves = [];

		// Loop through class job ids and get achievements
		foreach($classJobIds as $cjId => $achieveId) {
			if (array_key_exists($achieveId, $this->tempdata['achievements'])) {
				$achieves[$cjId] = $this->tempdata['achievements'][$achieveId];
			}
		}

		$this->add('achievements', $achieves);
	}

	//
	// Do some statistics for each class
	//
	private function handleExpStatistics()
	{
		// if no events, skip
		if (!$this->tempdata['events']) {
			return;
		}

		foreach($this->chardata['data']['classjobs'] as $id => $role) {
			// vars
			$amounts = [];
			foreach($this->tempdata['events']['exp'] as $event) {
				if ($event['jobclass'] == $id) {
					$amounts[] = $event['gained'];
				}
			}

			if ($amounts) {
				// Work out the total exp gained over the duration
				$duration = count($amounts) * $this->expPerHourDuration;
				$totalExp = array_sum($amounts);
				$seconds = ($duration * TIME_60MINUTES);

				// work out the average exp per second
				$expSecond = round($totalExp / $seconds, 5);
				$expMinute = $expSecond * TIME_60SECONDS;
				$expHour = $expSecond * TIME_60MINUTES;
				$expSession = $expSecond * TIME_60MINUTES * $this->expPerHourDuration;

				$this->add('exp_stats_'. $id, [
					'duration' => $duration,
					'total' => $totalExp,
					'seconds' => $seconds,
					'exp_second' => $expSecond,
					'exp_minute' => $expMinute,
					'exp_hour' => $expHour,
					'exp_session' => $expSession,
				]);
			}
		}
	}

	//
	// Handle tracking durations
	//
	private function handleTrackingDuration()
	{
		// if no events, ignore
		if (!$this->tempdata['events']['exp']) {
			return;
		}

		$start = 0;
		$finish = 0;

		$arr = array_merge($this->tempdata['events']['exp'], $this->tempdata['events']['lvs']);

		foreach($arr as $event) {
			$unixtime = strtotime($event['time']);

			if (!$start || $unixtime < $start) {
				$start = $unixtime;
			}

			if (!$finish || $unixtime > $finish) {
				$finish = $unixtime;
			}
		}

		$this->add('events_duration_start', $start);
		$this->add('events_duration_finish', $finish);
	}

	//
	// Handle the tracking legend
	//
	private function handleTrackingLegend()
	{
		// if no events, ignore
		if (!$this->tempdata['events']['exp']) {
			return;
		}

		$legend = [];
		$arr = array_merge($this->tempdata['events']['exp'], $this->tempdata['events']['lvs']);

		foreach($arr as $event) {
			$id = $event['jobclass'];
			$name = $this->chardata['data']['classjobs'][$id]['data']['name'];

			$legend[$name] = isset($legend[$name]) ? $legend[$name] + 1 : 1;
		}

		arsort($legend);
		$this->add('events_role_count', $legend);
	}

	//
	// Build an graph graph
	//
	private function handleExpGainedGraphData()
	{
		$graphdata = [];
		foreach($this->chardata['data']['classjobs'] as $role) {
			$name = $role['data']['name'];
			$exp = $role['exp']['total_gained'];
			$graphdata[$name] = $exp;
		}

		arsort($graphdata);
		$this->add('total_exp_graph', $graphdata);
	}

	//
	// Get the bext exp and level events
	//
	private function handleBestExpAndLevelsEvent()
	{
		// if no events, ignore
		if (!$this->tempdata['events']['exp']) {
			return;
		}

		//
		// Best EXP Event
		//
		$best = 0;
		$bestEvent = null;
		foreach($this->tempdata['events']['exp'] as $event) {
			// get best and ensure old wasn't zero (avoid those)
			if ($event['gained'] > $best && $event['old'] != 0) {
				$best = $event['gained'];
				$bestEvent = $event;
			}
		}

		if ($bestEvent) {
			$this->add('best_exp_event', $bestEvent);
		}

		//
		// Best levelling event
		//
		$best = 0;
		$bestEvent = null;
		foreach($this->tempdata['events']['lvs'] as $event) {
			// get best and ensure old wasn't zero (avoid those)
			if ($event['gained'] > $best && $event['old'] != 0) {
				$best = $event['gained'];
				$bestEvent = $event;
			}
		}

		if ($bestEvent) {
			$this->add('best_lv_event', $bestEvent);
		}
	}

	//
	// Get the bext exp and level events per role
	//
	private function handleBestExpAndLevelsEventPerClass()
	{
		// if no events, ignore
		if (!$this->tempdata['events']['exp']) {
			return;
		}

		foreach($this->chardata['data']['classjobs'] as $id => $role) {
			$events = [];

			//
			// Best EXP Event
			//
			$best = 0;
			$bestEvent = null;
			foreach($this->tempdata['events']['exp'] as $i => $event) {
				// get best and ensure old wasn't zero (avoid those)
				if ($event['jobclass'] == $id && $event['gained'] > $best && $event['old'] != 0) {
					$best = $event['gained'];
					$bestEvent = $event;
				}

				if ($event['jobclass'] == $id) {
					$event['type'] = 'exp';
					$event['unix'] = (new \DateTime($event['time']))->format('U');
					$events[] = $event;
				}
			}

			if ($bestEvent) {
				$this->add('best_exp_event_'. $id, $bestEvent);
			}

			//
			// Best levelling event
			//
			$best = 0;
			$bestEvent = null;
			foreach($this->tempdata['events']['lvs'] as $event) {
				// get best and ensure old wasn't zero (avoid those)
				if ($event['jobclass'] == $id && $event['gained'] > $best && $event['old'] != 0) {
					$best = $event['gained'];
					$bestEvent = $event;
				}

				if ($event['jobclass'] == $id) {
					$event['type'] = 'lvs';
					$event['unix'] = (new \DateTime($event['time']))->format('U');
					$events[] = $event;
				}
			}

			if ($bestEvent) {
				$this->add('best_lv_event_'. $id, $bestEvent);
			}

			//
			// Events
			//
			if ($events) {
				$this->sksort($events, 'unix');
				$this->add('events_'. $id, $events);
			}
		}
	}

	//
	// Work out the progress to level 50 for all roles combined.
	//
	private function handleProgressToAllMaxed()
	{
		$totalExpGained = 0;
		$totalExpMax = 0;
		$totalLvsGained = 0;
		$totalLvsMax = 0;

		// loop through class jobs and add up exp and total exp
		foreach($this->chardata['data']['classjobs'] as $role) {
			$totalExpGained = $totalExpGained + $role['exp']['total_gained'];
			$totalExpMax = $totalExpMax + $role['exp']['total_max'];

			$totalLvsGained = $totalLvsGained + $role['level'];
			$totalLvsMax = $totalLvsMax + GAME_MAX_LEVEL;
		}

		$this->add('total_exp_gained', $totalExpGained);
		$this->add('total_exp_max', $totalExpMax);
		$this->add('total_exp_togo', $totalExpMax - $totalExpGained);
		$this->add('total_exp_gained_percent', round($totalExpGained / $totalExpMax, 3) * 100);
		$this->add('total_lvs_gained', $totalLvsGained);
		$this->add('total_lvs_max', $totalLvsMax);
		$this->add('total_lvs_togo', $totalLvsMax - $totalLvsGained);
		$this->add('total_lvs_gained_percent', round($totalLvsGained / $totalLvsMax, 3) * 100);
	}
}
