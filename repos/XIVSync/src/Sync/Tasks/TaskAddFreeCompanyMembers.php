<?php

namespace Sync\Tasks;

//
// Add free company members
//
class TaskAddFreeCompanyMembers
{
	private $FreeCompany;
	private $Characters;

	function __construct()
	{
		$this->FreeCompanyMembers = new \Sync\App\FreeCompanyMembers();
		$this->Characters = new \Sync\App\Characters();
	}

	public function init($start)
	{
		$list = $this->FreeCompanyMembers->getLastScanned($start);
		if (!$list) {
            output('No free companies to add!');
			return;
		}

		$completed = $this->runAction($list);
		$timePassed = time() - START_TIME;

		output('Added %s free companies members to pending in %s seconds.', [ $completed, $timePassed ]);
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

			$data = [];

			// get character data (also parses it)
			$url = sprintf(LODESTONE_FREECOMPANY_MEMBERS_URL, $freecompany['fc_id']);
			$newData = $this->FreeCompanyMembers->requestFromLodestone($url);
			$data[] = $newData;

			if ($newData['page_total'] > 1) {
				for ($i = 2; $i <= $newData['page_total']; $i++) {
					output('Getting page: '. $i);
					$url = sprintf(LODESTONE_FREECOMPANY_MEMBERS_URL, $freecompany['fc_id']) . '?page='. $i;
					$newData = $this->FreeCompanyMembers->requestFromLodestone($url);
					$data[] = $newData;
				}
			}

			// add all ids to pending
			foreach($data as $page) {
				$this->Characters->addToPending($page['ids']);
				$complete += count($page['ids']);
			}

			$this->FreeCompanyMembers->updateScanned($freecompany['fc_id']);
		}

		return $complete;
	}
}
