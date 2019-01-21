<?php

namespace Sync\Tasks;

/**
 * Class TaskUpdateCharacters
 * @package Sync\Tasks
 */
class TaskUpdateCharacters
{
	private $Characters;
	private $FreeCompany;

    /**
     * TaskUpdateCharacters constructor.
     */
	function __construct()
	{
		$this->Characters = new \Sync\App\Characters();
		$this->FreeCompany = new \Sync\App\FreeCompany();
	}

    /**
     * @param $start
     */
	public function init($start)
	{
		$list = $this->Characters->getLastUpdatedCharacters($start);
		if (!$list) {
            output('No characters to update! (Weird!)');
			return;
		}

		output('');
		$response = $this->runAction($list);
		$timePassed = time() - START_TIME;
		output('Updated %s (%s deleted) characters in %s seconds.', [
		    $response['success'], $response['deleted'], $timePassed
        ]);

		$percentSuccess = $response['success'] == 0 ? 0 : (round($response['success'] / AUTO_UPDATE_MAX, 6) * 100);
        $percentFailed = $response['deleted'] == 0 ? 0 : (round($response['deleted'] / AUTO_UPDATE_MAX, 6) * 100);

		// write out
        $statline = sprintf('Time Now: %s|Queue: %s|TaskUpdateCharacters|Success: %s/%s (%s%%)|Deleted: %s/%s (%s%%)|Duration: %s (seconds)|Memory: %s|CPU: %s',
            timestamp(), $start,
            $response['success'], AUTO_UPDATE_MAX, $percentSuccess,
            $response['deleted'], AUTO_UPDATE_MAX, $percentFailed,
            $timePassed, memory(), cpu()
        );
		file_put_contents(ROOT.'/logging', $statline . "\n", FILE_APPEND);
	}

	//
	// Loop through all characters
	//
	private function runAction($list)
	{
		$response = [
		    'success' => 0,
            'deleted' => 0,
        ];

		foreach($list as $character)
		{
			// get time remaining
			$timePassed = time() - START_TIME;
			if ($timePassed >= TIME_LIMIT) {
				output('Breaking due to %s seconds passed', [ $timePassed ]);
				break;
			}

			// get character data (also parses it)
			$url = sprintf(LODESTONE_CHARACTERS_URL, $character['lodestone_id']);
			$newData = $this->Characters->requestFromLodestone($url);
			if (!$newData) {
                $response['deleted']++;
				output('Skipping as lodestone did not return any data (Character likely deleted)');
				$this->Characters->setDeleted($character['lodestone_id']);
				continue;
			}

			// manage character
			$newData = (new \Sync\App\CharactersRoles())->manage($newData);
			$newData = (new \Sync\App\CharactersPets())->manage($newData);

			// get old data
			$oldSavedData = $this->Characters->getOldData($character['lodestone_id']);
			$oldData = json_decode($oldSavedData['data'], true);
			$gcData = json_decode($oldSavedData['data_gc'], true);

			// tracking
			(new \Sync\App\CharactersEvents())->manage($oldData, $newData);
			(new \Sync\App\CharactersTracking())->manage($oldData, $newData);
			(new \Sync\App\CharactersGearsets())->manage($newData);
			$gcData = (new \Sync\App\CharactersGrandCompanies())->manage($gcData, $newData);

			// generate hash before we remove stuff
            $newHash = $this->Characters->generateActiveHash($newData);

            // if different, save it
            if ($newHash !== $character['data_hash']) {
                //$newHashData = $this->Characters->generateActiveHash($newData, true);
                //@file_put_contents(ROOT .'/hash/'. $character['lodestone_id'] .'_'. $newHash .'_'. time(), $newHashData);
            }

			// Add FC to pending
			if ($fcId = $newData['free_company']) {
				$this->FreeCompany->addToPending($fcId);
			}

			// remove stats and gear as I save them with gearsets
			unset($newData['stats']);
			unset($newData['gear']);

			// remove free company, it's handled internally
			unset($newData['free_company']);

			// update character
			$this->Characters->update(
                $character,
                $newData,
                $gcData,
                $newHash
			);

			// commit
            $response['success']++;
		}

		return $response;
	}
}
