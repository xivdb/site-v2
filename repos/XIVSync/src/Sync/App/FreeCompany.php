<?php

namespace Sync\App;

//
// FreeCompany class (db actions)
//
use Sync\Modules\Database;

class FreeCompany
{
	private $http;
	private $parser;

	function __construct()
	{
		$this->http = new \Sync\Modules\HttpRequest();
		$this->parser = new \Sync\Parser\FreeCompany();
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
	// Add Free Company to pending
	//
	public function addToPending($id)
	{
		// insert
        $dbs = new Database();
		$dbs->QueryBuilder
			->insert('pending_freecompanies')
			->schema([ 'fc_id' ])
			->values([ $id ], true)
			->duplicate([ 'fc_id' ], true);

		$dbs->execute();
	}

	//
	// Get the last updated free companies from the database
	//
	public function getLastUpdated($start)
	{
		global $start;

        $dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('freecompanies')
			->where('deleted != 1')
			->order('last_updated', 'asc')
			->limit(($start * AUTO_UPDATE_MAX), AUTO_UPDATE_MAX);

		return $dbs->get()->all();
	}

	//
	// Get the last added free companie from the database
	//
	public function getLastAdded($start)
	{
        $dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('pending_freecompanies')
			->order('added', 'asc')
			->where('processed IS NULL')
			->limit(($start * AUTO_ADD_MAX), AUTO_ADD_MAX);

		return $dbs->get()->all();
	}

	//
	// Get the last member scanned free company from the database
	//
	public function getLastMemberScanned($start)
	{
        $dbs = new Database();
		$dbs->QueryBuilder
			->select('*', false)
			->from('freecompanies')
			->order('last_member_scan', 'asc')
			->limit(($start * AUTO_ADD_MAX), AUTO_ADD_MAX);

		return $dbs->get()->all();
	}

	//
	// Add a free company
	//
	public function add($data)
	{
		// insert
        $dbs = new Database();
		$dbs->QueryBuilder
			->insert('freecompanies')
			->schema([
				'fc_id', 'server', 'tag', 'crest', 'last_updated',
			])
			->values([
				$data['id'], $data['server'], $data['tag'],
				json_encode($data['crest']), timestamp(),
			], true)
			->duplicate(['fc_id']);

		$dbs->execute();

		// insert
		$dbs->QueryBuilder
			->insert('freecompanies_data')
			->schema([ 'fc_id', 'data' ])
			->values([ $data['id'], json_encode($data) ], true)
			->duplicate(['data'], true);

		$dbs->execute();

		// update pending
		$dbs->QueryBuilder
			->update('pending_freecompanies')
			->set('processed', ':time')
			->bind('time', timestamp())
			->where('fc_id = :id')
			->bind('id', $data['id']);

		$dbs->execute();
	}

	//
	// Set a free company as deleted
	//
	public function setDeleted($id)
	{
        $dbs = new Database();
		$dbs->QueryBuilder
			->update('pending_freecompanies')
			->set('deleted', 1)
			->set('processed', ':time')->bind('time', timestamp())
			->where('fc_id = '. $id);

		$dbs->execute();
	}
}
