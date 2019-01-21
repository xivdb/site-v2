<?php

namespace XIVDB\Apps\Characters;

trait DataClassJobs
{
	private $expdata = [];

	protected function handleClassJobs()
	{
		$this->getExpDataFromDatabase();
		$this->createClassJobStatistics();
		$this->createActiveClassJobStatistics();
	}

	//
	// Get EXP Data from the database
	//
	private function getExpDataFromDatabase()
	{
		$expdata = [];
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select(['level', 'exp'])
			->from('xiv_param_grow')
			->where(['level >= 0', 'level < '. GAME_MAX_LEVEL]);

		foreach($dbs->get()->all() as $data) {
			$expdata[$data['level']] = ($data['level'] == 0) ? 0 : $data['exp'];
		}

		$this->expdata = $expdata;
	}

	//
	// Class job statistics
	//
	private function createClassJobStatistics()
	{
		$classjobs = $this->chardata['data']['classjobs'];
		$list = [];
		foreach($classjobs as $i => $classjob) {
			// get classjob
			$cj = $this->content->addClassJob($classjob['id']);
			$cj['icon'] = SECURE . sprintf('/img/classes/set2/%s.png', $cj['icon']);
			$classjob['data'] = $cj;

			// handle EXP calculations
			$classjob['exp']['at_cap'] = false;
			$classjob['exp']['total_gained'] = $this->getTotalGainedExp($classjob);
			$classjob['exp']['total_max'] = array_sum($this->expdata);
			$classjob['exp']['total_togo'] = $classjob['exp']['total_max'] - $classjob['exp']['total_gained'];

			// handle some level calculations
			$classjob['level_togo'] = GAME_MAX_LEVEL - $classjob['level'];
			if ($classjob['level'] > 0) {
				$classjob['level_percent'] = round($classjob['level'] / GAME_MAX_LEVEL, 3) * 100;
			} else {
				$classjob['level_percent'] = 0;
			}

			// if max level, then the percentages should be 100%
			if ($classjob['level'] == GAME_MAX_LEVEL) {
				$classjob['exp']['percent'] = 100;
				$classjob['exp']['total_percent'] = 100;
				$classjob['exp']['at_cap'] = true;
			} else if ($classjob['level'] == 0) {
				$classjob['exp']['percent'] = 0;
				$classjob['exp']['total_percent'] = 0;
			} else {
				$classjob['exp']['percent'] = $classjob['exp']['current'] > 0 ? round($classjob['exp']['current'] / $classjob['exp']['max'], 5) * 100 : 0;
				$classjob['exp']['total_percent'] = $classjob['exp']['total_gained'] > 0 ? round($classjob['exp']['total_gained'] / $classjob['exp']['total_max'], 5) * 100 : 0;
			}

			// save
			$list[$cj['id']] = $classjob;
		}

		$this->chardata['data']['classjobs'] = $list;
	}

	//
	// Active class job statistics
	//
	private function createActiveClassJobStatistics()
	{
		$activeRole = $this->chardata['data']['active_class'];

		// get classjob
		$activeRole = $this->content->addClassJob($activeRole['id']);
		$activeRole['icon'] = SECURE . sprintf('/img/classes/set2/%s.png', $activeRole['icon']);

		// if job
		if ($activeRole['is_job']) {
			$parentClassJob = $this->content->addClassJob($activeRole['classjob_parent']);
			$activeRoleProgress = $this->chardata['data']['classjobs'][$parentClassJob['id']];
		} else {
			$activeRoleProgress = $this->chardata['data']['classjobs'][$activeRole['id']];
		}

		$this->chardata['data']['active_class'] = [
			'role' => $activeRole,
			'progress' => $activeRoleProgress,
		];
	}

	//
	// Work out the total EXP gained
	//
	private function getTotalGainedExp($cj)
	{
		$expGained = 0;
		foreach($this->expdata as $level => $exp) {
			if ($level == $cj['level']) {
				break;
			}

			$expGained = $expGained + $exp;
		}

		return $expGained + $cj['exp']['current'];
	}
}
