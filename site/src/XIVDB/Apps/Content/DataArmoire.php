<?php

namespace XIVDB\Apps\Content;

class DataArmoire extends Data
{
	function __construct()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select('*', false)
			->from('xiv_cabinet')
			->where(['item > 0', '`order` > 0'])
			->order('`order`', 'asc');

		$list = $dbs->get()->all();

		$this->data = [
			'list' => $this->addItems($list),
		];
	}

	//
	// Add items to the list
	//
	private function addItems($list)
	{
		$content = $this->getModule('content');
		$content->setFlag('extended', true);

		foreach($list as $i => $entry)
		{
			$item = $content->addItem($entry['item']);
			if (!$item) {
				unset($list[$i]);
				continue;
			}

			$patch = $content->addPatch($entry['patch']);

			$list[$i]['item'] = $item;
			$list[$i]['patch'] = $patch;
		}

		return $list;
	}
}
