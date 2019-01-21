<?php

namespace XIVDB\Apps\Tools\ShoppingCart;

trait CartStatistics
{
	// Stats about the materials used
	protected $stats = [
		'totalShards' => 0,
		'totalItems' => 0,
		'totalUniqueItems' => 0,
		'totalInventorySlots' => 0,

		'itemCounts' => [],
		'shardCounts' => [],
		'itemSlotCounts' => [],

		'itemUsage' => [],
	];

	public function statistics()
	{
		// get total shards and items
		$this->getTotalShardsAndItems();

		// get total inventory slots
		$this->getInventorySlots();
	}


	//
	// Get total shards and items
	//
	private function getTotalShardsAndItems()
	{
		foreach($this->materialRequiredQuantity as $tier => $categories)
		{
			foreach($categories as $category => $itemlist)
			{
				// index key based on if category is shards or not
				$key = $category == 'shards' ? 'totalShards' : 'totalItems';
				$this->stats[$key] = array_sum($itemlist);

				// stat by tier
				$key = $category == 'shards' ? 'shardCounts' : 'itemCounts';
				$this->stats[$key][$tier] = array_sum($itemlist);

				// add to item usage
				foreach($itemlist as $id => $quantity)
				{
					$this->stats['itemUsage'][$id]
						= isset($this->stats['itemUsage'][$id])
							? $this->stats['itemUsage'][$id] + $quantity
							: $quantity;
				}
			}
		}
	}

	//
	// Get total unique items
	//
	private function getInventorySlots()
	{
		$unique = [];

		foreach($this->materialRequiredQuantity as $tier => $categories)
		{
			foreach($categories['items'] as $id => $quantity)
			{
				// increment inventory slots
				$this->stats['totalInventorySlots'] += ceil($quantity / $this->items[$id]['stack_size']);

				// inventory slots
				$this->stats['itemSlotCounts'][$tier]
					= isset($this->stats['itemSlotCounts'][$tier])
						? $this->stats['totalInventorySlots'] + ceil($quantity / $this->items[$id]['stack_size'])
						: ceil($quantity / $this->items[$id]['stack_size']);

				// unique items
				if (!in_array($id, $unique)) {
					$unique[] = $id;
				}
			}
		}

		$this->stats['totalUniqueItems'] = count($unique);
	}
}
