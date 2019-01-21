<?php

namespace XIVDB\Apps\Tools\ShoppingCart;

trait CartCheckout
{
	private $materialDistribution = [];
	private $materialRequiredQuantity = [];
	private $materialTierCreation = [];

	//
	// Checkout the main items the user wants
	//
	public function checkout()
	{
		// loop through the base items and get the materials required to build them
		foreach($this->basket as $id => $item)
		{
			// add the item data to the items list
			$this->items[$id] = $item['data'];

			if (!isset($item['data']['craftable'][0])) {
				continue;
			}

			// get recipe
			$recipe = $item['data']['craftable'][0];
			$this->recipes[$recipe['id']] = $recipe;

			// get materials from craftable tree
			$this->getMaterials($id, $recipe['tree'], 1);
		}
	}

	//
	// Simply gets all materials required for each tier, does no multiplcation
	//
	private function getMaterials($makeId, $makeTree, $tier)
	{
		if ($tier > 1) {
			$arr = $this->materialDistribution[$tier - 1][$makeId];
			if ($arr) {
				$qty = array_sum($arr);
				$this->materialTierCreation[$tier][$makeId] = $qty;
			}
		}

		foreach($makeTree as $synthItem)
		{
			$synthId = $synthItem['id'];
			$synthQuantity = $synthItem['quantity'];

			// add item to items array for easy access
			$this->items[$synthId] = $synthItem;

			// Add to material distribution
			$this->addToMaterialDistribution($tier, $synthId, $makeId, $synthQuantity);

			// Add material to required quantity
			$this->addToRequiredQuantity($tier, $synthId, $synthQuantity);

			// has tier 2?
			if (isset($synthItem['synths']) && $synthItem['synths'])
			{
				$recipe = reset($synthItem['synths']);
				$this->recipes[$recipe['id']] = $recipe;

				$this->getMaterials($recipe['item']['id'], $recipe['tree'], $tier + 1);
			}
		}
	}

	//
	// Add item to the required quantity
	//
	private function addToRequiredQuantity($tier, $synthId, $synthQuantity)
	{
		$key = $synthId > 100 ? 'items' : 'shards';

		$this->materialRequiredQuantity[$tier][$key][$synthId]
			= isset($this->materialRequiredQuantity[$tier][$key][$synthId])
				? $this->materialRequiredQuantity[$tier][$key][$synthId] + $synthQuantity
				: $synthQuantity;
	}

	//
	// Add an item to its synth distribution
	//
	private function addToMaterialDistribution($tier, $synthId, $makeId, $synthQuantity)
	{
		$this->materialDistribution[$tier][$synthId][$makeId]
			= isset($this->materialDistribution[$tier][$synthId][$makeId])
				? $this->materialDistribution[$tier][$synthId][$makeId] + $synthQuantity
				: $synthQuantity;
	}
}
