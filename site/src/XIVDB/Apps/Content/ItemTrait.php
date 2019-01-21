<?php

namespace XIVDB\Apps\Content;

trait ItemTrait
{
	//
	// Handle item special shops
	//
	private function handleSpecialShops()
    {
        $dbs = $this->getModule('database');

        // Add special shops
        $this->data['special_shops_obtain'] = null;
        $this->data['special_shops_currency'] = null;

        // The fields to check against
        $fields = [
            ['connect_specialshop_receive_1', 'receive_item_1', 'obtain'],
            ['connect_specialshop_receive_2', 'receive_item_2', 'obtain'],
            ['connect_specialshop_cost_1', 'cost_item_1', 'currency'],
            ['connect_specialshop_cost_2', 'cost_item_2', 'currency'],
            ['connect_specialshop_cost_3', 'cost_item_3', 'currency'],
        ];

        foreach($fields as $f)
        {
            $col = sprintf('special_shops_%s', $f[2]);

            if ($this->hasColumn($f[0])) {
                $dbs->QueryBuilder->select('*')->from(ContentDB::TO_SPECIAL_SHOP)->where($f[1] .' = :id')->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    $shopId = $res['special_shop'];

                    // add shop
                    if ($specialShopData = $this->addSpecialShop($shopId)) {
	                    $sd = array_merge(['special_usage' => $f[2]], $specialShopData);
	                    $sd['items'] = $res;

	                    // add items
	                    $fields = ['receive_item_1', 'receive_item_2', 'cost_item_1', 'cost_item_2', 'cost_item_3'];
	                    foreach($fields as $idx) {
	                        $sd['items'][$idx] = $sd['items'][$idx] ? $this->addItem($sd['items'][$idx]) : null;
	                    }

	                    $sd['items']['quest_item_1'] = $sd['items']['quest_item_1'] ? $this->addQuest($sd['items']['quest_item_1']) : null;

	                    $this->data[$col][] = $sd;
					}
                }
            }
        }
    }
}
