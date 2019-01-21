<?php

/**
 * Item
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\LibraData\ExtractClasses;

class Item extends \XIVDB\Apps\LibraData\LibraData
{
	const TABLE = 'xiv_items';

	public $real =
	[
		'Level' => 'level_item',
		'EquipLevel' => 'level_equip',
	];

	protected function manual()
	{
		$this->lodestonePaths();
		$this->instances();
	}

	//
	// Import some lodestone path info
	//
	private function lodestonePaths()
	{
		$insert = [];
		foreach($this->data as $id => $data)
        {
			$lodestone = explode('/', $data['path']);

			$insert[] = [
				'id' => $id,
				'icon_lodestone' => explode('.', $data['icon'])[0],
				'icon_lodestone_hq' => explode('.', $data['icon_hq'])[0],
				'lodestone_type' => $lodestone[0],
				'lodestone_id' => $lodestone[1],
			];
		}

		$this->insert(static::TABLE, $insert);
	}

	//
	// Some basic info
	//
	private function basicInfo()
	{
		$insert = [];
		foreach($this->data as $id => $data)
        {
			$json = json_decode($data['data'], true);

			if (isset($json['sell_price']) && $json['sell_price'] > 0) {
				$insert[] = [
					'id' => $id,
				];
			}
		}

		$this->insert(static::TABLE, $insert);
	}

	//
	// Attach items to instance content
	//
	private function instances()
	{
		$insert = [];
		foreach($this->data as $id => $data)
		{
			$json = json_decode($data['data'], true);

			// instances
            if (isset($json['instance_content']))
			{
                foreach($json['instance_content'] as $i => $instanceId) {
                    $insert[] = [
                        'item' => $id,
                        'instance' => $instanceId,
                        'patch' => $this->patch,
                    ];
                }
            }
		}

		$this->insert('xiv_instances_to_items', $insert);
	}
}
