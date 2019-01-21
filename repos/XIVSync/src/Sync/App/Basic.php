<?php

namespace Sync\App;

//
// Provide basic app functionality
//
use Sync\Modules\Database;

class Basic
{
	//
	// Get statistics for update progress
	//
	public function getUpdateStatistics()
	{
		$queries = [
			'select count(*) as total from characters where last_updated >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)',
			'select count(*) as total from characters where last_updated >= DATE_SUB(NOW(),INTERVAL 1 HOUR)',
			'select count(*) as total from characters where last_updated >= DATE_SUB(NOW(),INTERVAL 1 DAY)',
			'select count(*) as total from characters where added >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)',
			'select count(*) as total from characters where added >= DATE_SUB(NOW(),INTERVAL 1 HOUR)',
			'select count(*) as total from characters where added >= DATE_SUB(NOW(),INTERVAL 1 DAY)',
			'select count(*) as total from characters where achievements_last_updated >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)',
			'select count(*) as total from characters where achievements_last_updated >= DATE_SUB(NOW(),INTERVAL 1 HOUR)',
	        'select count(*) as total from characters where achievements_last_updated >= DATE_SUB(NOW(),INTERVAL 1 DAY)',
			'select count(*) as total from characters',
		];

        $dbs = new Database();
		foreach($queries as $i => $sql) {
			$total = $dbs->sql($sql);
			$total = $total[0]['total'];
			$queries[$i] = $total;
		}

		return $queries;
	}
}
