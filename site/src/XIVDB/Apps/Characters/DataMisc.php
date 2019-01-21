<?php

namespace XIVDB\Apps\Characters;

//
// Handle misc data
//
trait DataMisc
{
	protected function handleData()
	{
		$this->handleDecode();
		$this->handleAvatarTimestamps();
		$this->handleLastActive();
		$this->handleProfileUrls();
		$this->handleEventsData();
		$this->handleTrackingData();
	}

	private function handleAvatarTimestamps()
	{
		// modify some images
		$this->chardata['avatar'] = $this->chardata['avatar'] .'?t='. date('YzG');
		$this->chardata['portrait'] = $this->chardata['data']['portrait'] .'?t='. date('YzG');
	}

	//
	// Handle when the character was last active
	//
	private function handleLastActive()
	{
		if ($this->chardata['data_last_changed'] == 0) {
			$this->chardata['last_active']= 0;
		}

		$dataLastChanged = $this->chardata['data_last_changed'];
		$achievementsLastChanged = $this->chardata['achievements_last_changed'];

		// if the achievements last changed after the profile, set last active
		// to that timestamp, otherwise it goes to the profile last change timestamp

		if (strtotime($achievementsLastChanged) > strtotime($dataLastChanged)) {
			return $this->chardata['last_active'] = $achievementsLastChanged;
		}

		$this->chardata['last_active'] = $dataLastChanged;
	}

	//
	// Decode some of the data
	//
	private function handleDecode()
	{
		$this->chardata['data'] = json_decode($this->tempdata['storage_data']['data'], true);
		$this->chardata['grand_companies'] = json_decode($this->tempdata['storage_data']['data_gc'], true);
		$this->chardata['portrait'] = $this->chardata['data']['portrait'];
	}

	//
	// Sets up time formats and ensures events
	// are ordered by newest first.
	//
	private function handleEventsData()
	{
		if (!$this->tempdata['events']['exp'] && !$this->tempdata['events']['lvs']) {
			return;
		}

		foreach($this->tempdata['events'] as $type => $events) {
			foreach($events as $i => $event) {
				$event['type'] = $type;
				$event['timeline_id'] = $type;
				$this->tempdata['events'][$type][$i] = $event;
			}

			// sort by time
			$this->sksort($this->tempdata['events'][$type], 'time');
		}
	}

	//
	// Handle tracking information dates
	//
	private function handleTrackingData()
	{
		// if no tracking, just skip
		if (!$this->tempdata['tracking']) {
			return;
		}

		foreach($this->tempdata['tracking'] as $i => $track) {
			$track['timeline_id'] = 'tracking';
			$this->tempdata['tracking'][$i] = $track;
		}
	}

	//
	// Handle profile urls
	//
	private function handleProfileUrls()
	{
		$id = $this->chardata['lodestone_id'];
		$server = $this->chardata['server'];
		$name = $this->chardata['name'];

		// create urls
		$this->chardata['url'] = $this->url(static::TYPE, $server, $name);
		$this->chardata['url_api'] = API . $this->url(static::TYPE, $id);
		$this->chardata['url_xivdb'] = URL . $this->url(static::TYPE, $server, $name);
		$this->chardata['url_lodestone'] = 'https://na.finalfantasyxiv.com/lodestone/character/'. $id;
		$this->chardata['url_type'] = static::TYPE;
	}
}
