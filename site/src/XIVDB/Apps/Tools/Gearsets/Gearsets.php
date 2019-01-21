<?php

namespace XIVDB\Apps\Tools\Gearsets;

//
// Gearsets
//
class Gearsets extends \XIVDB\Apps\AppHandler
{
	private $types = [];

	function __construct()
	{
		$languages = $this->getModule('language');

		$this->types = [
			0 => $languages->custom(435),
			1 => $languages->custom(327),
			2 => $languages->custom(331),
			3 => $languages->custom(329),
			4 => $languages->custom(234),
			5 => $languages->custom(235),
		];
	}

	//
	// Save a gearset
	//
	public function save($name, $desc, $type, $classjob, $json)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->insert('content_gearsets')
			->schema([
				'user', 'name', 'description',
				'type', 'class', 'data'
			])
			->values([
				$this->getUser()->id,
				':name', ':desc', ':type',
				':classjob', ':json'
			])
			->bind('name', $name)
			->bind('desc', $desc)
			->bind('type', $type)
			->bind('classjob', $classjob)
			->bind('json', $json);

		return $dbs->execute();
	}

	//
	// Update a gearset
	//
	public function update($id, $name, $desc, $type, $classjob, $json)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->update('content_gearsets')
			->set([
				'name' => ':name',
				'description' => ':desc',
				'type' => ':type',
				'class' => ':classjob',
				'data' => ':json',
			])
			->where(['id = :id', 'user = :user'])
			->bind('name', $name)
			->bind('desc', $desc)
			->bind('type', $type)
			->bind('classjob', $classjob)
			->bind('json', $json)
			->bind('id', $id)
			->bind('user', $this->getUser()->id);

		return $dbs->execute();
	}

	//
	// Load a gearset
	//
	public function load()
	{
		$classjob = $this->getModule('gamedata')->xivGameDataBasic('xiv_classjobs');
		$data = $this->getUser()->gearsets;

		foreach($data as $i => $arr) {
			$data[$i]['data'] = json_decode($arr['data'], true);

			// fix up class icon
			$class = $classjob[$arr['class']];
			$class['name'] = $class['name_'. LANGUAGE];
			$class['abbr'] = $class['abbr_'. LANGUAGE];

			// type/class
			$data[$i]['type_name'] = $this->types[$arr['type']];
			$data[$i]['class'] = $class;
		}

		return $data;
	}
}
