<?php

namespace XIVDB\Apps\Community;

//
// Screenshots tally trait
//
trait ScreenshotsTally
{
	//
	// Get a total amount based on a start date
	//
	public function getTotalSinceDate($date = false)
	{
		$this->dbs
			->QueryBuilder
			->total()
			->from(self::TABLE);

		return $this->dbs->get()->total();
	}

	//
	// Get the top commented content
	//
	public function getTopOverall($total = 10)
	{
		// Custom SQL
		$sql = sprintf('SELECT uniq, content, count(*) as total
		FROM %s
		GROUP BY uniq, content
		ORDER BY total DESC
		LIMIT 0,%s', self::TABLE, $total);

		$data = $this->dbs->sql($sql);

		// get content
		$content = $this->getModule('content');

		foreach($data as $i => $d) {
			$data[$i]['data'] = $content->setCid($d['content'])->getContentToId($d['content'], $d['uniq']);

			if (!$data[$i]['data']) {
				unset($data[$i]);
			}
		}

		return $data;
	}
}
