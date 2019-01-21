<?php

namespace XIVDB\Apps\Services\XIVSync;

trait Database
{
	//
	// Search for an item
	//
	public function searchItem($name)
	{
		$url = '/database/item/search?name='. $name;
		return $this->api($url);
	}

	//
	// Search for an item
	//
	public function getItem($id)
	{
		return $this->api('/database/item/get/'. $id);
	}
}
