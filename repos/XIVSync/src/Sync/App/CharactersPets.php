<?php

namespace Sync\App;

//
// Character roles
//
class CharactersPets
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

		$this->handleMinions();
		$this->handleMounts();

		return $this->data;
	}

	// Minions

	private function handleMinions()
	{
	    if (!isset($this->data['minions'])) {
	        return;
        }

		foreach($this->data['minions'] as $i => $minion)
		{
			$minion = $this->getMinionId($minion);
			$this->data['minions'][$i] = $minion;
		}
	}

	private function getMinionId($name)
	{
		$minions = $this->xivdb->get('minions');

		foreach($minions as $min) {
			if (strtolower($min['name_en']) == strtolower($name)) {
				return $min['id'];
			}
		}

		return false;
	}

	// Mounts

	private function handleMounts()
	{
	    if (!isset($this->data['mounts'])) {
	        return;
        }

		foreach($this->data['mounts'] as $i => $minion)
		{
			$minion = $this->getMountId($minion);
			$this->data['mounts'][$i] = $minion;
		}
	}

	private function getMountId($name)
	{
		$mounts = $this->xivdb->get('mounts');

		foreach($mounts as $mnt) {
			if (strtolower($mnt['name_en']) == strtolower($name)) {
				return $mnt['id'];
			}
		}

		return false;
	}
}
