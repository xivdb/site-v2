<?php

/**
 * Materia
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Materia extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_materia';

    protected $real =
    [
        11 => 'base_param',
    ];

    protected $set =
    [
        1 => 'item_1',
        2 => 'item_2',
        3 => 'item_3',
        4 => 'item_4',
        5 => 'item_5',
        6 => 'item_6',
        7 => 'item_7',
        8 => 'item_8',
        9 => 'item_9',
        10 => 'item_10',

        12 => 'value_1',
        13 => 'value_2',
        14 => 'value_3',
        15 => 'value_4',
        16 => 'value_5',
        17 => 'value_6',
        18 => 'value_7',
        19 => 'value_8',
        20 => 'value_9',
        21 => 'value_10',
    ];

	protected function manual()
    {
        $this->addStats();
    }

	//
	// Set materia stats
	//
	private function addStats()
	{
		$insert = [];

		$quickLoop = [
			[1, 11, 12],
			[2, 11, 13],
			[3, 11, 14],
			[4, 11, 15],
			[5, 11, 16],
			[6, 11, 17],
			[7, 11, 18],
			[8, 11, 19],
			[9, 11, 20],
			[10, 11, 21],
		];

		// loop through materia
		foreach($this->getCsvFileData() as $id => $line)
		{
			// loop through quick array
			foreach($quickLoop as $os)
			{
				// get item, param and value
				$itemId = $line[$os[0]];
				$param = $line[$os[1]];
				$value = $line[$os[2]];

				// if item, add insert entry
				if ($itemId > 0) {
					$insert[] = [
	                    'item' => $itemId,
	                    'baseparam' => $param,
	                    'value' => $value,
	                    'value_hq' => $value,
	                ];
				}
			}
		}

		// insert into base params
		$this->insert('xiv_items_to_baseparam', $insert);
	}
}
