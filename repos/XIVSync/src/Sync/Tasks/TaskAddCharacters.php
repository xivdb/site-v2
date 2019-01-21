<?php

namespace Sync\Tasks;

//
// Add characters
//
class TaskAddCharacters
{
	private $Characters;

	function __construct()
	{
		$this->Characters = new \Sync\App\Characters();
	}

	public function init($start)
	{
		$list = $this->Characters->getLastAdded($start);
		if (!$list) {
            output('No characters to add!');
			return;
		}

		$completed = $this->runAction($list);
		$timePassed = time() - START_TIME;

		output('Added %s characters in %s seconds.', [ $completed, $timePassed ]);
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

			// get character data (also parses it)
			$url = sprintf(LODESTONE_CHARACTERS_URL, $character['lodestone_id']);
			$newData = $this->Characters->requestFromLodestone($url);
			if (!$newData) {
				// TODO : Add "set deleted" + "processed" here.
				output('Skipping as lodestone did not return any data (Character likely deleted)');
				$this->Characters->setDeleted($character['lodestone_id']);
				continue;
			}

			// manage character
			$newData = (new \Sync\App\CharactersRoles())->manage($newData);
			$newData = (new \Sync\App\CharactersPets())->manage($newData);
			$gcData = (new \Sync\App\CharactersGrandCompanies())->manage([], $newData);
			(new \Sync\App\CharactersGearsets())->manage($newData);

			// generate hash before we remove stuff
			$hash = $this->Characters->generateActiveHash($newData);

			// remove stats and gear as I save them with gearsets
			unset($newData['stats']);
			unset($newData['gear']);

			// remove free company, it's handled internally
			unset($newData['free_company']);

			// Add character
			$this->Characters->add($newData, $gcData, $hash);

			$complete++;
		}

		return $complete;
	}
}
