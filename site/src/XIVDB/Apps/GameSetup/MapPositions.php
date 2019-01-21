<?php

namespace XIVDB\Apps\GameSetup;

class MapPositions extends \XIVDB\Apps\AppHandler
{
	use \XIVDB\Apps\GameData\GameDatabase;

	const TABLE = 'content_maps';

	public function init()
	{
		$this->handleNPCPositions();
		$this->handleFatePositions();
		$this->handleEnemyPositions();
	}

	//
	// Handle all NPC positions
	//
	public function handleNPCPositions()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder->select('*')->from('xiv_npc')->where("position != ''");

		// build npc map positions
		$insert = [];
		foreach($dbs->get()->all() as $data)
		{
			$position = explode(',', $data['position']);
			$insert[] = [
				'type_id' => 11,
				'content_id' => $data['id'],
				'placename' => $data['placename'],
				'icon' => '/img/game_map_icons/npc.png',
				'x' => $position[0],
				'z' => $position[1],
				'manual' => 0,
			];
		}

		$this->insert(self::TABLE, $insert);
	}

	//
	// Handle all FATE positions
	//
	public function handleFatePositions()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder->select('*')->from('xiv_fates')->where("position != ''");

		// build npc map positions
		$insert = [];
		foreach($dbs->get()->all() as $data)
		{
			$position = explode(',', $data['position']);
			$insert[] = [
				'type_id' => 7,
				'content_id' => $data['id'],
				'placename' => $data['placename'],
				'icon' => '',
				'x' => $position[0],
				'z' => $position[1],
				'manual' => 0,
			];
		}

		$this->insert(self::TABLE, $insert);
	}

	//
	// Handle all Enemy positions
	//
	public function handleEnemyPositions()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder->select('*')->from('xiv_npc_enemy')->where("position != '' AND position != '0'");

		// build npc map positions
		$insert = [];
		foreach($dbs->get()->all() as $data)
		{
			$position = explode(',', $data['position']);
			$insert[] = [
				'type_id' => 12,
				'content_id' => $data['id'],
				'placename' => $data['placename'],
				'icon' => '',
				'x' => $position[0],
				'z' => $position[1],
				'manual' => 0,
			];
		}

		$this->insert(self::TABLE, $insert);
	}
}
