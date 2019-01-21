<?php

namespace Sync\App;

//
// Character grand companies
//
class CharactersGrandCompanies
{
	// Set character data
	public function manage($gcData, $newData)
	{
		$lodestoneId = $newData['id'];

		$gc = $newData['grand_company'];

		// if the character doesn't have a gc, return
		if (!$gc) {
			return null;
		}

		// reset the grand company data if this is first time
		if (!$gcData) {
			$gcData = [];
		}

		// record last time this gc was updated
		$gc['time'] = timestamp();

		// save
		$index = str_ireplace(' ', '_', strtolower($gc['name']));
		$gcData[$index] = $gc;
		return $gcData;
	}
}
