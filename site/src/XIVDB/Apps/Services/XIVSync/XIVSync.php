<?php

namespace XIVDB\Apps\Services\XIVSync;

//
// Talk to XIVSync, my own service :D
//
class XIVSync extends \XIVDB\Apps\AppHandler
{
	use Characters;
	use Achievements;
	use Lodestone;
	use Database;

	public $errors = [];

	//
	// Query the XIVSync API
	//
	public function api($route, $decode = true)
	{
	    $prefix = stripos($route, '?') === false ? '?' : '&';

		// create xivsync url
		$url = XIVSYNC . $route;
		$url = str_ireplace(' ', '+', $url) . $prefix .'c='. time() .'&language=en';

		// options for file get contents due to it being on ssl and xivdb not.
		// short timeout as the service may go down.
		$options = [
			'http' => [
				'timeout' => 90,
			],
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
			],
		];

		// get data
		$data = file_get_contents($url, false, stream_context_create($options));
		if ($data) {
			// if to decode or not
			if ($decode) {
				$data = json_decode($data, true);
			}

			return $data;
		}

		$this->errors[] = error_get_last();
		return false;
	}

	public function isOnline()
	{
		return $this->api('/online', false, false) ? true : false;
	}
}
