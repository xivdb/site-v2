<?php

namespace XIVDB\Apps\Tools\Mapper;

trait Saver
{
    /**
     * @param $hash
     * @param $entry
     * @param $position
     * @param $data
     * @param $map
     */
	protected function save($hash, $entry, $position, $data, $map)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->insert('app_mapper')
			->schema(['hash', 'content_type', 'name', 'id', 'map', 'placename', 'position', 'ingame_xy', 'data'])
			->values([':hash', ':type', ':name', ':id', ':map', ':placename', ':position', ':ingame_xy', ':data'])
			->bind('hash', $hash)
			->bind('type', $entry['type'])
			->bind('name', $entry['name'])
			->bind('id', $entry['id'])
			->bind('map', $entry['map'])
			->bind('placename', $map['placename'])
            ->bind('ingame_xy', sprintf('%s,%s', $position['ingame']['x'], $position['ingame']['y']))
			->bind('position', json_encode($position))
			->bind('data', json_encode($data))
			->duplicate(['hash']);

		$dbs->execute();
	}
}
