<?php

namespace XIVDB\Apps\Tools\Mapper;

trait Placenames
{
	// hold maps so we don't query per loop
	private $xivPlacenamesMaps = [];

    /**
     * @param $id
     * @return mixed
     */
	protected function getPlacenameFromMapId($id)
	{
		// get if not already set
		// - this needs adding to redis!
		if (!isset($this->xivPlacenamesMaps[$id]))
		{
			$dbs = $this->getModule('database');
			$dbs->QueryBuilder
				->select('*')
				->from('xiv_placenames_maps')
				->where('id = :id')
				->bind('id', $id)
				->limit(0,1);

			$this->xivPlacenamesMaps[$id] = $dbs->get()->one();
		}

		return $this->xivPlacenamesMaps[$id];
	}
}
