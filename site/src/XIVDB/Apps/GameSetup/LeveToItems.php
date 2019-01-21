<?php

namespace XIVDB\Apps\GameSetup;

class LeveToItems extends \XIVDB\Apps\AppHandler
{
	use \XIVDB\Apps\GameData\GameDatabase;

	const TABLE = 'xiv_leves_to_items';

	public function init()
	{
		$this->handleItems();
	}

	//
	// Handle all NPC positions
	//
	public function handleItems()
	{
		$dbs = $this->getModule('database');

		// get leves
		$dbs->QueryBuilder->select('id, leve_reward_group')->from('xiv_leves');

		// insert
		$insert = [];
		foreach($dbs->get()->all() as $leve)
		{
			$leveId = $leve['id'];

			// Get group
			$dbs->QueryBuilder
				->select('id, group')
				->from('xiv_leves_reward_groups')
				->where('id = '. $leve['leve_reward_group']);

			// go through groups
			foreach($dbs->get()->all() as $group)
			{
				// Get group
				$dbs->QueryBuilder
					->select('id, item, quantity')
					->from('xiv_leves_reward_items')
					->where('id = '. $group['group']);

				foreach($dbs->get()->all() as $item)
				{
					$insert[] = [
						'leve' => $leveId,
						'item' => $item['item'],
						'quantity' => $item['quantity'],
					];
				}
			}
		}

		$this->insert(self::TABLE, $insert);
	}
}
