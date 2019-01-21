<?php

namespace Sync\App;

/**
 * Class CharacterFollowing
 * @package Sync\App
 */
class CharacterFollowing
{
	private $dbs;
	private $http;
	private $parser;

    /**
     * CharacterFollowing constructor.
     */
	function __construct()
	{
		$this->dbs = new \Sync\Modules\Database();
		$this->http = new \Sync\Modules\HttpRequest();
		$this->parser = new \Sync\Parser\CharacterFollowing();
	}

    /**
     * Request character data from lodestone
     * @param $url
     * @return array|bool
     */
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

    /**
     * todo : logic
     * Get the last updated free companies from the database
     * @param $start
     * @return mixed
     */
	public function getLastScanned($start)
	{
		$this->dbs->QueryBuilder
			->select('*', false)
			->from('freecompanies')
			->order('last_member_scan', 'asc')
			->limit(($start * AUTO_UPDATE_MAX), AUTO_UPDATE_MAX);

		return $this->dbs->get()->all();
	}

    /**
     * todo : logic
     * Update Fc's last scanned time
     * @param $id
     * @return mixed
     */
	public function updateScanned($id)
	{
		$this->dbs->QueryBuilder
			->update('freecompanies')
			->set('last_member_scan', ':time')->bind('time', timestamp())
			->where('fc_id = :id')->bind('id', $id);

		return $this->dbs->get()->all();
	}
}
