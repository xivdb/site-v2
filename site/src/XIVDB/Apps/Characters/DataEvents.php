<?php

namespace XIVDB\Apps\Characters;

trait DataEvents
{
	private $eventsRecentLimit = 5;

	//
	// Handle events
	//
	protected function handleEvents()
	{
		// if no events, ignore
		if (!$this->tempdata['events']) {
			return;
		}

		$this->getRecentEvents();
	}

	//
	// Get some of the latest events
	//
	private function getRecentEvents()
	{
		$recent = [];
		foreach($this->tempdata['events'] as $type => $events) {
			foreach($events as $i => $event) {
				if (count($recent) <= $this->eventsRecentLimit) {
					$recent[] = $event;
				}
			}
		}

		$this->tempdata['events_data']['recent'] = $recent;
	}
}
