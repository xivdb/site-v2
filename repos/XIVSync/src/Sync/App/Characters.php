<?php

namespace Sync\App;

use Sync\Modules\Database;

/**
 * Class Characters
 * @package Sync\App
 */
class Characters
{
	private $http;
	private $parser;

	function __construct()
	{
		$this->http = new \Sync\Modules\HttpRequest();
		$this->parser = new \Sync\Parser\Character();
	}

	//
	// Request character data from lodestone
	//
	public function requestFromLodestone($url)
	{
		$html = $this->http->get($url);
		if (!$html) {
			return false;
		}

		$data = $this->parser->parse($html);
		if (!$data) {
			return false;
		}

		return $data;
	}

	//
	// Get the last updated characters from the database
	//
	public function getLastUpdatedCharacters($start)
	{
		$dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('characters')
			->where('deleted != 1');

		//
		// Get a date restriction, this means more active people will update.
		//
		if (AUTO_UPDATE_TIME_RESTRICTED)
		{
			switch($start)
			{
			    // 7 days
                case 0:
                case 1:
                case 2:
                case 3:
                case 4:
                    $priority = 1; break;

                // 30 days
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                    $priority = 2; break;

                // 30+ days
                case 10:
                case 11:
                case 12:
                case 13:
                case 14:
                case 15:
                case 16:
                case 17:
                case 18:
                case 19:
                case 20:
                default:
                    $priority = 3; break;
			}

            output('Updating priority members: '. $priority);
			$dbs->QueryBuilder
                ->forceIndex('auto_update')
                ->where('priority = :p')->bind('p', $priority);
		}

		// start offset based on previous priority
		$startOffset = [
		    0 => 0,
		    1 => 1,
		    2 => 2,
		    3 => 3,
		    4 => 4,

		    5 => 0,
		    6 => 1,
		    7 => 2,
            8 => 3,
		    9 => 4,

		    10 => 0,
		    11 => 1,
		    12 => 2,
            13 => 3,
		    14 => 4,
		    15 => 5,
            16 => 6,
            17 => 7,
            18 => 8,
            19 => 9,
            20 => 10,
        ];

		output('Update number: '. $start);
		$start = AUTO_UPDATE_TIME_RESTRICTED ? ($startOffset[$start] * AUTO_UPDATE_MAX) : ($start * AUTO_UPDATE_MAX);
		output(sprintf('Updating characters between: %s - %s', $start, $start + AUTO_UPDATE_MAX));

		$dbs->QueryBuilder
			->order('last_updated', 'asc')
			->limit($start, AUTO_UPDATE_MAX);

		return $dbs->get()->all();
	}

	//
	// Gets a single updated entry
	//
	public function getUpdatedEntry($id)
	{
		$dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('characters')
			->where('lodestone_id = :id')
			->bind('id', $id);

		return $dbs->get()->one();
	}

	//
	// Get the last added characters from the database
	//
	public function getLastAdded($start)
	{
		$dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('pending_characters')
			->order('added', 'asc')
			->where('processed IS NULL')
			->where('deleted != 1')
			->limit(($start * AUTO_ADD_MAX), AUTO_ADD_MAX);

		return $dbs->get()->all();
	}

	//
	// Add a character
	//
	public function add($data, $gcData, $hash)
	{
		$dbs = new Database();

		$json = json_encode($data);
		$gcData = json_encode($gcData);

		// insert
		$dbs->QueryBuilder
			->insert('characters')
			->schema([
				'lodestone_id',
                'last_updated',
                'data_last_changed',
				'data_hash',
                'name', 
                'server',
                'avatar',
                'priority',
			])
			->values([
				$data['id'],
                timestamp(),
                0,
                $hash,
				$data['name'],
                $data['server'],
                $data['avatar'],
                1,
			], true)
			->duplicate(['name', 'server'], true);

		$dbs->execute();

		// insert data
		$dbs->QueryBuilder
			->insert('characters_data')
			->schema([ 'lodestone_id', 'data', 'data_gc' ])
			->values([ $data['id'], $json, $gcData ], true)
			->duplicate(['data'], true);

		$dbs->execute();

		// update pending
        $this->addToPending([ $data['id'] ]);
		$dbs->QueryBuilder
			->update('pending_characters')
			->set('processed', ':time')
			->bind('time', timestamp())
			->where('lodestone_id = :id')
			->bind('id', $data['id']);

		$dbs->execute();
	}

	//
	// Add to pending
	//
	public function addToPending($ids)
	{
		$dbs = new Database();
		$dbs->QueryBuilder
			->insert('pending_characters')
			->schema([ 'lodestone_id' ]);

		foreach($ids as $id) {
			$dbs->QueryBuilder->values([ $id ], true);
		}

		$dbs->QueryBuilder->duplicate(['lodestone_id'], true);

		$dbs->execute();
	}

	//
	// Update a character
	//
	public function update($character, $data, $gcData, $newHash)
	{
		$dbs = new Database();
        $character = (Object)$character;

		$json = json_encode($data);
		$gcData = json_encode($gcData);
		$lastActive = $character->data_last_changed;

		// check if the characters data has updated
        output('Compare hash:'. $newHash .' == '. $character->data_hash);
        if (AUTO_UPDATE_SET_LAST_ACTIVE && $newHash != $character->data_hash) {
            $lastActive = timestamp();
        }

        $nowUnix = time();
        $lastActiveUnix = strtotime($lastActive);
        $lastActiveDiff = $nowUnix - $lastActiveUnix;

        // work out queue priority:
        $priority = 3;
        if ($lastActiveDiff < TIME_1WEEK) {
            $priority = 1;
        } else if ($lastActiveDiff < TIME_30DAYS) {
            $priority = 2;
        }

		$dbs->QueryBuilder
			->update('characters')
			->set('last_updated', ':last_updated')->bind('last_updated', timestamp())
			->set('data_hash', ':hash')->bind('hash', $newHash)
			->set('name', ':name')->bind('name', $data['name'])
			->set('server', ':server')->bind('server', $data['server'])
			->set('avatar', ':avatar')->bind('avatar', $data['avatar'])
			->set('update_count', $character->update_count + 1)
            ->set('priority', ':p')->bind('p', $priority)
			->where('lodestone_id = :id')->bind('id', $data['id']);

		// if hash has changed, update timestamp
		if (AUTO_UPDATE_SET_LAST_ACTIVE && $newHash != $character->data_hash) {
			$dbs->QueryBuilder->set('data_last_changed', ':dlc')->bind('dlc', timestamp());
		}

		$dbs->execute();

		// todo : store this in flat file
		// insert
		$dbs->QueryBuilder
			->update('characters_data')
			->set('data', ':data')->bind('data', $json)
			->set('data_gc', ':data_gc')->bind('data_gc', $gcData)
			->where('lodestone_id = :id')->bind('id', $data['id']);

		$dbs->execute();
	}

	//
	// Get data
	//
	public function getOldData($id)
	{
		$dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('characters_data')
			->where('lodestone_id = :id')->bind('id', $id);

		return $dbs->get()->one();
	}

	//
	// Set a character as deleted
	//
	public function setDeleted($id)
	{
		$dbs = new Database();
		$dbs->QueryBuilder
			->update('pending_characters')
			->set('deleted', 1)
			->where('lodestone_id = '. $id);

		$dbs->execute();
		$dbs->QueryBuilder
			->update('characters')
			->set('deleted', 1)
            ->set('priority', 9)
			->set('last_updated', ':last_updated')
			->bind('last_updated', timestamp())
			->where('lodestone_id = '. $id);

		$dbs->execute();
	}

	//
	// Generate a hash based on character data
	//
	public function generateActiveHash($data, $getData = false)
	{
		// remove grand company icon
		if (isset($data['grand_company']['icon'])) {
			unset($data['grand_company']['icon']);
		}

        // remove guardian icon
        if (isset($data['guardian']['icon'])) {
            unset($data['guardian']['icon']);
        }

        // remove city icon
        if (isset($data['city']['icon'])) {
            unset($data['city']['icon']);
        }

		// ignore avatar and portrait
		unset($data['avatar']);
		unset($data['portrait']);
        unset($data['free_company']); // being kicked from an FC does not make you active.

        // remove gear
        unset($data['gear']);
        unset($data['stats']);

        $json = json_encode($data);

        if ($getData) {
            return $json;
        }

		$sha1 = sha1($json);
		return $sha1;
	}
}
