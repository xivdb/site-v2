<?php

namespace XIVDB\Apps\Characters;

trait DataGear
{
	protected function handleGear()
	{
		#$this->getActiveGear();
		$this->getGearSetItems();
		$this->getGearSetsGraphs();
	}

	//
	// Get the players gearset items
	//
	protected function getGearSetItems()
	{
		// item levels which are doubled
		$ilvDoubled = [
			1, 3, 4, 5, 7, 9, 10, 84, 87, 88, 89, 96, 97 ,98
		];

		$slots = [
			'slot_mainhand', 'slot_offhand', 'slot_head', 'slot_body', 'slot_hands',
			'slot_waist', 'slot_legs', 'slot_feet', 'slot_necklace',
			'slot_earrings', 'slot_bracelets', 'slot_ring1', 'slot_ring2',
			'slot_soulcrystal'
		];

		$this->tempdata['gearslots'] = $slots;

		foreach($this->tempdata['gearsets'] as $i => $gear) {
			$ilvTotal = 0;
			$ilvCalc = 0;
			$ilvSlots = [];

			$activeRole = $this->content->addClassJob($gear['classjob_id']);
			$activeRole['icon'] = SECURE . sprintf('/img/classes/set2/%s.png', $activeRole['icon']);
			$gear['role'] = $activeRole;

			foreach($slots as $j => $slot) {
				$itemId = isset($gear[$slot]) ? $gear[$slot] : null;
				if (!$itemId) {
					continue;
				}

				$item = $this->content->addItem($itemId);
				$item['slot_column'] = $slot;

				// add data to slot
				$gear['gear'][$slot]['data'] = $item;

				if ($item['slot_equip'] != 13) {
					// Increment total item level
					$ilvTotal = $ilvTotal + $item['level_item'];

					// Increment item level for calculation
					$ilvCalc = $ilvCalc + $item['level_item'];

					// Set item level slots for graphdata
					$ilvSlots[$slot] = $item['level_item'];
					$ilvSlotsNames[$slot] = $item['slot_name'];
				}

				// if item ui category in ilv double list, add ilv again
				if (in_array($item['item_ui_category'], $ilvDoubled)) {
					$ilvCalc = $ilvCalc + $item['level_item'];
				}
			}

			$gear['item_level_total'] = $ilvTotal;
			$gear['item_level_avg'] = floor($ilvCalc / 13);
			$gear['item_level_graph'] = $ilvSlots;
			$gear['item_level_graph_names'] = $ilvSlotsNames;

			if ($ilvSlots) {
			    $gear['item_level_highest'] = max($ilvSlots);
            }


			unset($this->tempdata['gearsets'][$i]);
			$this->tempdata['gearsets'][$ilvTotal . $gear['classjob_id']] = $gear;
		}

		// sort gearsets by item level
		$this->sksort($this->tempdata['gearsets'], 'item_level_total');
	}

	//
	// Setup graph data for gearsets
	//
	private function getGearSetsGraphs()
	{
		foreach($this->tempdata['gearsets'] as $i => $gearset)
		{
			// loop through stat type
			foreach($gearset['stats'] as $type => $stats)
			{
				// graphdata
				$graphdata = [];

				// loop through values
				foreach($stats as $name => $value) {
					$name = $name == 'hp' ? 'HP' : $name;
					$name = $name == 'mp' ? 'MP' : $name;
					$name = $name == 'tp' ? 'TP' : $name;

					$graphdata[$name] = $value;
				}

				arsort($graphdata);
				$this->tempdata['gearsets'][$i]['stats_graph_data'][$type] = $graphdata;
			}
		}
	}
}
