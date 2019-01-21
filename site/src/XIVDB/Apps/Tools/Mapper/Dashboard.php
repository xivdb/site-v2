<?php

namespace XIVDB\Apps\Tools\Mapper;

/**
 * Class Dashboard
 * @package XIVDB\Apps\Tools\Mapper
 */
trait Dashboard
{
    /**
     * @param $id
     * @return mixed
     */
	public function getMap($id)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select(['id', 'name_{lang} as name'], false)
			->from('xiv_placenames')
			->where('id = :id')
			->bind('id', $id)
			->limit(0,1);

		return $dbs->get()->one();
	}

    /**
     * @return array
     */
	public function getSubmissionTotals()
	{
        $dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select(['placename', '[count]'], false)
			->from('app_mapper')
			->group('placename');

		$data = [];
		foreach($dbs->get()->all() as $res) {
			$data[$res['placename']] = $res['total'];
		}

		return $data;
	}

    /**
     * @return mixed
     */
	public function getMapList()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select([
				'xiv_placenames' => ['id', 'name_{lang} as name'],
                'app_mapper_claimed' => ['username','charactername']
			])
			->from('xiv_placenames')
			->join(['xiv_placenames' => 'id'], ['xiv_placenames_maps' => 'placename'])
            ->join(['xiv_placenames' => 'id'], ['app_mapper_claimed' => 'mapid'])
			->where('xiv_placenames_maps.placename != 0')
            ->where('xiv_placenames.id != xiv_placenames.region')
			->order('xiv_placenames.name_{lang}', 'asc');

		return $dbs->get()->all();
	}

    /**
     * @param $id
     * @return array
     */
	public function getPointsForMap($id)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select('*', false)
			->from('app_mapper')
			->where('placename = :id')
			->bind('id', $id);

		$data = [];
		foreach($dbs->get()->all() as $res) {
			$res['position'] = json_decode($res['position'], true);
			$data[] = $res;
		}

		return $data;
	}

    /**
     * @param $mapId
     * @param $user
     */
	public function claim($mapId, $user)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->insert('app_mapper_claimed')
			->schema(['mapid', 'username', 'charactername'])
			->values([':id', ':username', ':char'])
			->bind('id', $mapId)
			->bind('username', $user->username)
			->bind('char', $user->character_name)
			->duplicate(['username', 'char']);

		$dbs->execute();

		$this->getModule('session')->add('success', 'You have been set as working on this map!');
	}

    /**
     * @param $mapId
     * @return mixed
     */
	public function getClaimed($mapId)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select('*')
			->from('app_mapper_claimed')
			->where('mapid = :id')->bind('id', $mapId)
			->limit(0,1);

		return $dbs->get()->one();
	}

    /**
     * @param $hash
     */
	public function deletePoint($hash)
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->delete('app_mapper')
			->where('hash = :hash')->bind('hash', $hash);

		$dbs->execute();
		$this->getModule('session')->add('success', 'Deleted point!');

	}

    /**
     * Upload a json file
     *
     * @param $file
     */
	public function uploadJson($json)
    {
        $json = explode("\n", $json);

        $log = [];
        $log[] = sprintf('Parsing: %s lines', count($json));
        $count = 0;

        foreach($json as $line) {
            if (strlen($line) > 50)
            {
                $this->setData($line)->process();
                $count = $count + $this->count;

            }
        }

        $log[] = sprintf('Added %s entries', $count);
        $log = implode(" - ", $log);
        $this->getModule('session')->add('success', $log);
    }
}
