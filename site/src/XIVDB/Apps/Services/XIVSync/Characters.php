<?php

namespace XIVDB\Apps\Services\XIVSync;

trait Characters
{
	//
	// Verify a character biography against a usercode and a character id
	//
	public function verifyCharacter($usercode, $characterId)
	{
		$languages = $this->getModule('language');

		// get character
		$data = $this->api('/character/parse/' . $characterId);
        $this->api('/character/add/' . $characterId);

		if (!$data) {
			return $languages->custom(968);
		}

		$biography = $data['data']['biography'];

		// Check for user code in biography
		if (stripos($biography, $usercode) !== false) {
			return $data['data'];
		}

		// default response
		return $languages->custom(969, [
			'{usercode}' => $usercode,
		]);
	}

    /**
     * @param $lodestoneId
     * @return mixed
     */
	public function addCharacter($lodestoneId)
    {
        return $this->api('/character/add/'. $lodestoneId);
    }
}
