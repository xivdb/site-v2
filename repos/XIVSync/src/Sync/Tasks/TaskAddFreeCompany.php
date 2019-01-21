<?php

namespace Sync\Tasks;

//
// Add free company
//
class TaskAddFreeCompany
{
	private $FreeCompany;

	function __construct()
	{
		$this->FreeCompany = new \Sync\App\FreeCompany();
	}

	public function init($start)
	{
		$list = $this->FreeCompany->getLastAdded($start);
		if (!$list) {
            output('No free companies to add!');
			return;
		}

		$completed = $this->runAction($list);
		$timePassed = time() - START_TIME;

		output('Added %s free companies in %s seconds.', [ $completed, $timePassed ]);
	}

	//
	// Loop through all free companies
	//
	private function runAction($list)
	{
		$complete = 0;
		foreach($list as $freecompany)
		{
			// get time remaining
			$timePassed = time() - START_TIME;
			if ($timePassed >= TIME_LIMIT) {
				output('Breaking due to %s seconds passed', [ $timePassed ]);
				break;
			}

			// get character data (also parses it)
			$url = sprintf(LODESTONE_FREECOMPANY_URL, $freecompany['fc_id']);
			$newData = $this->FreeCompany->requestFromLodestone($url);
			if (!$newData) {
				// TODO : Add "set deleted" + "processed" here.
				output('Skipping as lodestone did not return any data (FreeCompany likely deleted)');
				$this->FreeCompany->setDeleted($freecompany['fc_id']);
				continue;
			}

			$this->FreeCompany->add($newData);
			$complete++;
		}

		return $complete;
	}
}
