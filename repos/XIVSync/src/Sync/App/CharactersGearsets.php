<?php

namespace Sync\App;

//
// Character gearsets
//
class CharactersGearsets
{
	private $dbs;
	private $xivdb;

	function __construct()
	{
		$this->dbs = new \Sync\Modules\Database();
		$this->xivdb = new \Sync\Modules\XIVDBApi();
	}

	// Set character data
	public function manage($newData)
	{
		$lodestoneId = $newData['id'];

		// get data
		$gear = $newData['gear'];
		$stats = json_encode($newData['stats']);
		$role = $newData['active_class'];

		// convert gear lodestone ids to gear ids
		$gear = $this->convertItemIds($gear);

		// if no gear (item missing on xivdb, don't save)
		if (!$gear) {
			return;
		}

		// save gearset for this class/job
		$this->dbs->QueryBuilder
			->insert('characters_gearsets')
			->schema([
				'lodestone_id', 'last_updated', 'classjob_id', 'level', 'stats',
				'slot_mainhand', 'slot_offhand', 'slot_head', 'slot_body', 'slot_hands', 'slot_waist',
				'slot_legs', 'slot_feet', 'slot_necklace', 'slot_earrings', 'slot_bracelets',
				'slot_ring1', 'slot_ring2', 'slot_soulcrystal',
			])
			->values([
				$lodestoneId, timestamp(), $role['id'], $role['level'], $stats,
				isset($gear['mainhand']) ? $gear['mainhand'] : null,
				isset($gear['offhand']) ? $gear['offhand'] : null,
				isset($gear['head']) ? $gear['head'] : null,
				isset($gear['body']) ? $gear['body'] : null,
				isset($gear['hands']) ? $gear['hands'] : null,
				isset($gear['waist']) ? $gear['waist'] : null,
				isset($gear['legs']) ? $gear['legs'] : null,
				isset($gear['feet']) ? $gear['feet'] : null,
				isset($gear['necklace']) ? $gear['necklace'] : null,
				isset($gear['earrings']) ? $gear['earrings'] : null,
				isset($gear['bracelets']) ? $gear['bracelets'] : null,
				isset($gear['ring1']) ? $gear['ring1'] : null,
				isset($gear['ring2']) ? $gear['ring2'] : null,
				isset($gear['soulcrystal']) ? $gear['soulcrystal'] : null
			])
			->duplicate(['lodestone_id']);

		$this->dbs->execute();
	}

    /**
     * @param $gear
     * @return bool
     */
	private function convertItemIds($gear)
	{
		$items = $this->xivdb->get('items');
        $temp = [];

		foreach($gear as $slot => $item)
		{
            $id = $item['id'];
            $name = $item['name'];

			$realId = isset($items[$id]) ? $items[$id]['id'] : false;
			if (!$realId) {
                $item = $this->xivdb->searchForItem($name);

                if ($item) {
                    $realId = $item['id'];
                }
			}

            $temp[$slot] = $realId;
		}

		return $temp;
	}
}
