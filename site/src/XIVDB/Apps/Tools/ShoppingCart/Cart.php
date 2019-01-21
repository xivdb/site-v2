<?php

namespace XIVDB\Apps\Tools\ShoppingCart;

use XIVDB\Apps\Content\ContentDB;

class Cart extends \XIVDB\Apps\AppHandler
{
	use CartCheckout;
	use CartStatistics;

	const MAX_QUANTITY = 99999;

	// Basket is the items requested by the user
	// including the quantity they want
	protected $basket = [];

	// items is just a list of items related to their
	// id so it can be referenced at any time
	protected $items = [];

	// list of recipes related to their id
	// so it can be referenced
	protected $recipes = [];

	//
	// Add an item to the cart
	//
	public function add($id, $quantity)
	{
		// max quantity
		if ($quantity > self::MAX_QUANTITY) {
			$quantity = self::MAX_QUANTITY;
		}

		// get item
		$item = $this->getContent($id, 'item', $quantity);

		// add item to cart
		$this->basket[$id] = [
			'id' => $id,
			'quantity' => $quantity,
			'data' => $item,
		];
	}

	//
	// Get items from cart
	//
	public function get()
	{
		/*show('materialDistribution');
		show($this->materialDistribution);
		show('-----------------------------------------------------------');
		show('materialRequiredQuantity');
		show($this->materialRequiredQuantity);
		show('-----------------------------------------------------------');
		show('materialTierCreation');
		show($this->materialTierCreation);
		show('-----------------------------------------------------------');
		show('stats');
		show($this->stats);
		show('-----------------------------------------------------------');
		die;*/

		return [
			'items' => $this->items,
			'basket' => $this->basket,
			'stats' => $this->stats,
			'recipes' => $this->recipes,
			'materialDistribution' => $this->materialDistribution,
			'materialRequiredQuantity' => $this->materialRequiredQuantity,
			'materialTierCreation' => $this->materialTierCreation,
		];
	}
	//
	// Get a piece of content
	//
	public function getContent($id, $type, $quantity = 1)
	{
		// get content class
		$cls = $this->getContentClass($type);
		$cls->setFlag('cart', true)
            ->setFlag('extended', true)
            ->setId($id)
            ->setQuantity($quantity);

		$data = $cls->get();

		// return data
		return $data;
	}
}
