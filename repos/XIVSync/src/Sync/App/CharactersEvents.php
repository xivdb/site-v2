<?php

namespace Sync\App;

//
// Character events
//
class CharactersEvents
{
	private $dbs;
	private $xivdb;

	private $lodestoneId;
	private $newData;
	private $oldData;

	function __construct()
	{
		$this->dbs = new \Sync\Modules\Database();
		$this->xivdb = new \Sync\Modules\XIVDBApi();
	}

	// Set character data
	public function manage($oldData, $newData)
	{
		$this->lodestoneId = $newData['id'];
		$this->newData = $newData;
		$this->oldData = $oldData;

		$this->check();
	}

	//
	// Check for new events
	//
	private function check()
	{
		// some vars
		$expTable = $this->xivdb->get('exp_table');
		$maxLevel = $expTable[count($expTable) - 1]['level'];
		$eventsLevels = [];
		$eventsExp = [];

		// get new and old data
		$newClassJobData = $this->newData['classjobs'];
		$oldClassJobData = $this->oldData['classjobs'];

		// loop through classjobs
		foreach($newClassJobData as $key => $newRole)
		{
			$oldRole = $oldClassJobData[$key] ?? false;

			// if you don't have an old role, skip
            if (!$oldRole) {
                continue;
            }

			// get total exp
			$oldTotalExp = $this->getTotalExp($oldRole);
			$newTotalExp = $this->getTotalExp($newRole);

			// if the OLD class is max level, we don't need to do anything
            // or if the new level is 0
            //
            // - this is a forced skip as we don't want to generate exp events
            //   even if this section fails.
            if ($oldRole['level'] >= $maxLevel || $newRole['level'] == 0) {
				continue;
			}

			// if the OLD level is higher than the NEW level, it broke!
            // (can happen on cache isues, just continue and ignore)
            //
            // - this is a forced skip as we don't want to generate exp events
            //   even if this section fails.
            if ($oldRole['level'] > $newRole['level']) {
				continue;
			}

			// If the old was 0 and the new was 30, its likely
            // to be one of the new jobs, skip the event as
            // that isn't really "earned"
            // (0 - 1317680 is the exp relative)
            if ($oldRole['level'] == 0 && $newRole['level'] == 30 || $oldTotalExp == 0 && $newTotalExp == 1317680) {
				continue;
			}

			// if the level has increased, create an event
			if ($newRole['level'] > $oldRole['level']) {
				$levelsGained = $newRole['level'] - $oldRole['level'];

				// if old or new is 0, skip.
				if ($oldRole['level'] == 0 || $newRole['level'] == 0) {
					continue;
				}

				// Double check it's not below zero
				if ($levelsGained > 0) {
					$eventsLevels[] = [
						'lodestone_id' => $this->lodestoneId,
						'time' => timestamp(),
						'jobclass' => $newRole['id'],
						'gained' => $levelsGained,
						'old' => $oldRole['level'],
						'new' => $newRole['level'],
					];
				}
			}

			// if exp has increased, create an event!
			if ($newTotalExp > $oldTotalExp) {
				$expGained = $newTotalExp - $oldTotalExp;

				// if old or new is 0, skip.
				if ($oldTotalExp == 0 || $newTotalExp == 0) {
					continue;
				}

				// Ensure the gained EXP is above the threshold limit
				if ($expGained > MINIMUM_EXP_FOR_EVENT) {
					$eventsExp[] = [
						'lodestone_id' => $this->lodestoneId,
						'time' => timestamp(),
						'jobclass' => $newRole['id'],
						'gained' => $expGained,
						'old' => $oldTotalExp,
						'new' => $newTotalExp,
					];
				}
			}
		}

		// if level events, save them
		if ($eventsLevels) {
			$this->dbs->QueryBuilder
				->insert('characters_events_lvs_new')
				->schema(array_keys($eventsLevels[0]))
				->duplicate(['lodestone_id']);

			foreach($eventsLevels as $event) {
				$this->dbs->QueryBuilder->values($event, true);
			}

			$this->dbs->execute();
		}

		// if exp events, save them
		if ($eventsExp) {
			$this->dbs->QueryBuilder
				->insert('characters_events_exp_new')
				->schema(array_keys($eventsExp[0]))
				->duplicate(['lodestone_id']);

			foreach($eventsExp as $event) {
				$this->dbs->QueryBuilder->values($event, true);
			}

			$this->dbs->execute();
		}
	}

	private function getTotalExp($role)
	{
		$expTable = $this->xivdb->get('exp_table');
		$total = 0;

		foreach($expTable as $row) {
			if ($row['level'] < $role['level']) {
				$total += $row['exp'];
			}
		}

		return $total + $role['exp']['current'];
	}
}
