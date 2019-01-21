<?php

namespace XIVDB\Apps\Characters;

trait DataTimeline
{
	protected function handleTimeline()
	{
		$this->tempdata['timeline'] = [];
		$this->tempdata['timeline_count'] = [];

		$this->getTimelineEvents();
		$this->getTimelineAchievements();
		$this->getTimelineTracking();

		// ksort all the things
		foreach($this->tempdata['timeline'] as $year => $years) {
			foreach($years as $month => $months) {
				krsort($this->tempdata['timeline'][$year][$month]);
			}

			krsort($this->tempdata['timeline'][$year]);
		}

		krsort($this->tempdata['timeline']);
	}

	//
	// increment count
	//
	private function incrementTimelineCount($year, $month)
	{
		$this->tempdata['timeline_count'][$year . $month] =
			isset($this->tempdata['timeline_count'][$year . $month])
				? $this->tempdata['timeline_count'][$year . $month] + 1
				: 1;
	}

	//
	// Handle timeline events
	//
	private function getTimelineEvents()
	{
		// if no events, skip
		if (!$this->tempdata['events']) {
			return;
		}

		// go through all events and add them to the timeline
		foreach($this->tempdata['events'] as $type => $events) {
			foreach($events as $i => $event) {
				$key = strtotime($event['time']);
				$year = (new \DateTime($event['time']))->format('Y');
				$month = (new \DateTime($event['time']))->format('n');

				// create placeholder
				if (!isset($this->tempdata['timeline'][$year][$month][$key])) {
					$this->tempdata['timeline'][$year][$month][$key] = [];
				}

				$this->tempdata['timeline'][$year][$month][$key][] = $event;
				$this->incrementTimelineCount($year, $month);
			}
		}
	}

	//
	// Handle timeline achievements
	//
	private function getTimelineAchievements()
	{
		// if achievements not public, skip.
		if (!$this->chardata['achievements_public']) {
			return;
		}

		// go through achievements and add them to the timeline
		foreach($this->tempdata['achievements'] as $achieve) {
			$key = strtotime($achieve['obtained']);
			$year = (new \DateTime($achieve['obtained']))->format('Y');
			$month = (new \DateTime($achieve['obtained']))->format('n');

			// create placeholder
			if (!isset($this->tempdata['timeline'][$year][$month][$key])) {
				$this->tempdata['timeline'][$year][$month][$key] = [];
			}

			$achieve['type'] = 'achievement';
			$this->tempdata['timeline'][$year][$month][$key][] = $achieve;
			$this->incrementTimelineCount($year, $month);
		}
	}

	//
	// Handle timeline tracking
	//
	private function getTimelineTracking()
	{
		// if no tracking, skip
		if (!$this->tempdata['tracking']) {
			return;
		}

		// go through tracking and add to the timeline
		foreach($this->tempdata['tracking'] as $track) {
			$key = strtotime($track['time']);
			$year = (new \DateTime($track['time']))->format('Y');
			$month = (new \DateTime($track['time']))->format('n');

			// create placeholder
			if (!isset($this->tempdata['timeline'][$year][$month][$key])) {
				$this->tempdata['timeline'][$year][$month][$key] = [];
			}

			$this->tempdata['timeline'][$year][$month][$key][] = $track;
			$this->incrementTimelineCount($year, $month);
		}
	}
}
