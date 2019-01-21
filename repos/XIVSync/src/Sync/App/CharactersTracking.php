<?php

namespace Sync\App;

//
// Character tracking
//
class CharactersTracking
{
	private $dbs;

	private $lodestoneId;
	private $newData;
	private $oldData;

	function __construct()
	{
		$this->dbs = new \Sync\Modules\Database();
	}

	// Set character data
	public function manage($oldData, $newData)
	{
		$this->lodestoneId = $newData['id'];
		$this->newData = $newData;
		$this->oldData = $oldData;

		// compare basic data
		$this->compareFirstTier('name');
		$this->compareFirstTier('server');
		$this->compareFirstTier('title');
		$this->compareFirstTier('race');
		$this->compareFirstTier('clan');
		$this->compareFirstTier('gender');
		$this->compareFirstTier('nameday');

		// compare second tier data
		$this->compareSecondTier('city', 'name');
		$this->compareSecondTier('grand_company', 'name');
		$this->compareSecondTier('grand_company', 'rank');
		$this->compareSecondTier('free_company', 'id');
	}

	private function add($type, $oldValue, $newValue)
	{
        output('New tracking entry: (%s) %s > %s', [ $type, $oldValue, $newValue ]);

		$this->dbs->QueryBuilder
			->insert('characters_events_tracking')
			->schema([ 'time', 'lodestone_id', 'type', 'old_value', 'new_value' ])
			->values([ timestamp(), $this->lodestoneId, $type, ':old', ':new' ])
			->bind('old', $oldValue)
			->bind('new', $newValue)
			->duplicate(['lodestone_id'], true);

		$this->dbs->execute();
	}

	// Track basic profile info
	private function compareFirstTier($field)
	{
		$oldValue = isset($this->oldData[$field]) ? $this->oldData[$field] : null;
		$newValue = isset($this->newData[$field]) ? $this->newData[$field] : null;

		// do nothing if any value does not exist
		if (!$oldValue || !$newValue) {
			return;
		}

		if ($oldValue != $newValue) {
			$this->add($field, $oldValue, $newValue);
		}
	}

	private function compareSecondTier($type, $field)
	{
		$oldValue = isset($this->oldData[$type][$field]) ? $this->oldData[$type][$field] : null;
		$newValue = isset($this->newData[$type][$field]) ? $this->newData[$type][$field] : null;

		// do nothing if any value does not exist
		if (!$oldValue || !$newValue) {
			return;
		}

		if ($oldValue != $newValue) {
			$this->add(sprintf('%s_%s', $type, $field), $oldValue, $newValue);
		}
	}
}
