<?php

namespace Sync\App;

//
// Character roles
//
class CharactersRoles
{
	private $XIVDB;
	private $data = [];

	function __construct()
	{
		$this->xivdb = new \Sync\Modules\XIVDBApi();
	}

	// Set character data
	public function manage($data)
	{
		$this->data = $data;

		$this->handleClassJobs();

		return $this->data;
	}

	// Handle a characters class/jobs
	private function handleClassJobs()
	{
		$classjobs = [];
		foreach($this->data['classjobs'] as $i => $cj)
		{
			// get id
			$id = $this->xivdb->getRoleId($cj['name']);

			// add id
			$cj['id'] = $id;

			// add to list
			$classjobs[$id] = $cj;
		}

		// resave list
		$this->data['classjobs'] = $classjobs;
	}
}
