<?php

namespace XIVDB\Apps\Tools\Icons;

//
// Comments class
//
class Icons extends \XIVDB\Apps\AppHandler
{
	//
	// Get folders in a directory
	//
	public function getFolders($route)
	{
		if (stripos($route, '..') !== false || stripos($route, '~') !== false) {
			exit('Where are you going?');
		}

		// ensure directory exists
		$directory = ROOT_IMAGES . $route;
		if (!is_dir($directory)) {
			exit('Nothing here... Go back where you were.');
		}

		$contents = array_diff(scandir($directory), ['..', '.']);
		$temp = [];
		foreach($contents as $node) {
			if (is_dir($directory .'/'. $node)) {
				if ($route) {
					$node = $route .'/'. $node;
				}
				$temp[] = $node;
			}
		}

		return $temp;
	}

	//
	// Get paths in a directory
	//
	public function getIcons($route)
	{
		if (stripos($route, '..') !== false) {
			exit('Where are you going?');
		}

		// ensure directory exists
		$directory = ROOT_IMAGES . $route;
		if (!is_dir($directory)) {
			exit('Nothing here... Go back where you were.');
		}

		$files = array_diff(scandir($directory), ['..', '.']);
		$temp = [];
		foreach($files as $file) {
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if (in_array(strtolower($ext), ['png', 'jpg', 'gif'])) {
			    $web = stripos($directory, '/web/');
			    $url = substr($directory, $web + 4);0
			    $url = sprintf('%s/%s', SECURE, $url);
			    $temp[] = $url .'/'. $file;
            }
		}

		return $temp;
	}
}
