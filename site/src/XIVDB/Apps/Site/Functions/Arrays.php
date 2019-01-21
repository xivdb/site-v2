<?php

namespace XIVDB\Apps\Site\Functions;

trait Arrays
{
	//
	// Gets a list of the "index" from an array
	//
	public function getValueListFromArray($array, $index)
	{
		$list = [];
		foreach($array as $i => $a) {
			$list[] = $a[$index];
		}

		return $list;
	}
}
