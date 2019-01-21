<?php

namespace XIVDB\Apps\Content;

class Data extends \XIVDB\Apps\AppHandler
{
	protected $data = [];

	//
	// Get all the data we have
	//
	public function get()
	{
		return $this->data;
	}
}
