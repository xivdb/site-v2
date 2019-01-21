<?php

namespace XIVDB\Apps\Site;

class Tracking
{
	private $tracking = [];

	//
	// Start a tracking call
	//
	public function start($name, $text = null)
	{
		if (!SITE_MONITOR) {
			return;
		}

		$this->tracking[$name] = [
			'name' => $name,
			'text' => $text,
			'start' => microtime(true),
		];
	}

	//
	// Finish a trackign call
	//
	public function finish($name, $text = null)
	{
		if (!SITE_MONITOR) {
			return;
		}

		if (!isset($this->tracking[$name])) {
			return;
		}

		$startData = $this->tracking[$name];
		unset($this->tracking[$name]);

		// if text, override
		if ($text) {
			$startData['text'] = $text;
		}

		$duration = round(microtime(true) - $startData['start'], 4);
		$line = sprintf("Duration: %s - Track: %s >> %s \n", $duration, $name, $startData['text']);
		file_put_contents(SITE_MONITOR_FILE, $line, FILE_APPEND);
	}
}
