<?php

namespace XIVDB\Apps\Characters;

use XIVDB\Apps\Content\ContentDB;

//
// Handle achievement data
//
trait DataAchievements
{
	private $achievementRecentLimit = 10;

	protected function handleAchievements()
	{
		// if achievements are not publick, ignore!
		if (!$this->chardata['achievements_public'] || $this->chardata['achievements_score_reborn'] == 0) {
			return;
		}

		$this->getAllAchievementsFromDatabase();
		$this->reOrderAchievementIndexes();
		$this->getAchievementContentData();
		$this->getAchievementPercentages();
		$this->getRecentAchievements();

		$this->groupAchievementsIntoCategories();
	}

	//
	// Get all the achievements from the database,
	// including category and kind.
	//
	private function getAllAchievementsFromDatabase()
	{
		// list of achievement ids possible to obtain
		$possible = $this->tempdata['achievements_possible']['possible'];
		$this->tempdata['all_achievements'] = [];

		if ($possible)
		{
			$possible = implode(',', $possible);
			
			// get possible list
			$dbs = $this->getModule('database');
			$dbs->QueryBuilder
				->select()
				->from(ContentDB::ACHIEVEMENTS)
				->addColumns([ ContentDB::ACHIEVEMENTS => ['id', 'name_{lang} as name', 'help_{lang} as help', 'points', 'icon'] ])
				->addColumns([ ContentDB::ACHIEVEMENTS_CATEGORY => ['id as category_id', 'name_{lang} as category_name'] ])
				->addColumns([ ContentDB::ACHIEVEMENTS_KIND => ['id as kind_id', 'name_{lang} as kind_name'] ])
                ->addColumns([ ContentDB::PATCH => ['name_{lang} as patch_name', 'command as patch_number', 'patch', 'patch_url'] ])
				->join([ ContentDB::ACHIEVEMENTS => 'achievement_category' ], [ ContentDB::ACHIEVEMENTS_CATEGORY => 'id' ])
	            ->join([ ContentDB::ACHIEVEMENTS_CATEGORY => 'achievement_kind' ], [ ContentDB::ACHIEVEMENTS_KIND => 'id' ])
                ->join([ ContentDB::ACHIEVEMENTS => 'patch' ], [ ContentDB::PATCH => 'patch' ]);

			$data = [];

			foreach($dbs->get()->all() as $i => $row) {
			    $data[$row['id']] = $row;
            }

			$this->tempdata['all_achievements'] = $data;
		}
	}

	//
	// Group all achievement into categories
	// - 1st: we first need to know the total points per category
	// - 2nd: loop through all obtained and work out total
	// - 3rd: create menu with all achievements, as: [category] => percent
	//
	private function groupAchievementsIntoCategories()
	{
		$categories = [];
		$all = [];

		//
		// 1st: we first need to know the total points per category
		//
		$totalPointsPerCategory = [];
		$totalCountPerCategory = [];
		foreach($this->tempdata['all_achievements'] as $i => $achieve)
		{
			$name = sprintf('%s|%s', $achieve['kind_name'], $achieve['category_name']);

			$catid = $achieve['category_id'];
			$categories[$catid] = $name;

			$totalPointsPerCategory[$catid] =
				isset($totalPointsPerCategory[$catid])
					? $totalPointsPerCategory[$catid] + $achieve['points']
					: $achieve['points'];

            $totalCountPerCategory[$catid] =
                isset($totalCountPerCategory[$catid])
                    ? $totalCountPerCategory[$catid] + 1
                    : 1;

			$achieve['url'] = $this->url('achievement', $achieve['id'], $achieve['name']);
			$achieve['icon'] = $this->iconize($achieve['icon']);
			$all[$catid][] = $achieve;
		}

		//
		// 2nd: loop through all obtained and work out total
		//
		$totalPointsPerCategoryObtained = [];
		$totalCountPerCategoryObtained = [];
		foreach($this->tempdata['achievements'] as $i => $achieve)
		{
			$catid = $achieve['category_id'];

			$totalPointsPerCategoryObtained[$catid] =
				isset($totalPointsPerCategoryObtained[$catid])
					? $totalPointsPerCategoryObtained[$catid] + $achieve['points']
					: $achieve['points'];

            $totalCountPerCategoryObtained[$catid] =
                isset($totalCountPerCategoryObtained[$catid])
                    ? $totalCountPerCategoryObtained[$catid] + 1
                    : 1;
		}

		//
		// 3rd: create menu with all achievements, as: [category] => percent
		//
		$menulist = [];
		foreach($totalPointsPerCategory as $id => $total) {
		    $totalCount = $totalCountPerCategory[$id];
			$obtained = isset($totalPointsPerCategoryObtained[$id]) ? $totalPointsPerCategoryObtained[$id] : 0;
            $obtainedCount = isset($totalCountPerCategoryObtained[$id]) ? $totalCountPerCategoryObtained[$id] : 0;
			$percent = $obtained > 0 ? round($obtained / $total, 4) * 100 : 0;
			$percentCount = $obtainedCount > 0 ? round($obtainedCount / $totalCount, 4) * 100 : 0;

			$catkind = explode('|', $categories[$id]);
			$menulist[$catkind[0]][$catkind[1]] = [
				'id' => $id,
				'kind' => $catkind[0],
				'category' => $catkind[1],
				'percent' => $percent,
				'obtained' => $obtained,
				'total' => $total,
                'percent_count' => $percentCount,
                'obtained_count' => $obtainedCount,
                'total_count' => $totalCount,
                'is_numbered' => in_array($id, [16]),
			];
		}

		$this->tempdata['achievements_menu'] = $menulist;
		$this->tempdata['achievements_all'] = $all;
	}

	//
	// Reorder achievement indexes to [id] > achievement
	//
	private function reOrderAchievementIndexes()
	{
		$list = [];
		foreach($this->tempdata['achievements'] as $i => $achieve) {
			$list[$achieve['achievement_id']] = $achieve;
		}

		$this->tempdata['achievements'] = $list;
	}

	//
	// Get the content data for the achievements
	//
	private function getAchievementContentData()
	{
		if (!$this->tempdata['achievements']) {
			return;
		}

		//
		// Achievements
		//
		foreach($this->tempdata['achievements'] as $i => $achieve) {
		    $achievement = $this->tempdata['all_achievements'][$achieve['achievement_id']];

		    $id = $achievement['id'];
		    $name = $achievement['name'];

		    // data
		    $achievement['icon'] = SECURE . $this->iconize($achievement['icon']);
			$achievement['timeline_id'] = 'achievement';
			$achievement['obtained'] = $achieve['obtained'];

            // urls
			$achievement['url'] = $this->url('achievement', $id, $name);
            $achievement['url_type'] = 'achievement';
            $achievement['url_api'] = API . $this->url('achievement', $id);
            $achievement['url_xivdb'] = URL . $this->url('achievement', $id, $name);

            // fix up patch
            $achievement['patch'] = [
                'name' => $achievement['patch_name'],
                'number' => $achievement['patch_number'],
                'patch' => $achievement['patch'],
                'url' => $achievement['patch_url'],
            ];

            ksort($achievement);
            $achievements[$i] = $achievement;
		}

		$this->tempdata['achievements'] = $achievements;
	}

	//
	// Get achievement percentages
	//
	private function getAchievementPercentages()
	{
		// Achievement scores
		$scoreReborn = $this->chardata['achievements_score_reborn'];
		$scoreLegacy = $this->chardata['achievements_score_legacy'];
		$scoreRebornTotal = $this->chardata['achievements_score_reborn_total'];
		$scoreLegacyTotal = $this->chardata['achievements_score_legacy_total'];

		// Percentages!
		$this->chardata['achievements_score_reborn_percent'] = $scoreReborn > 0 ? round($scoreReborn / $scoreRebornTotal, 5) * 100 : 0;
		$this->chardata['achievements_score_legacy_percent'] = $scoreLegacy > 0 ? round($scoreLegacy / $scoreLegacyTotal, 5) * 100 : 0;
	}

	//
	// Get recent obtained achievements
	//
	private function getRecentAchievements()
	{
		$achievements = [];
		foreach($this->tempdata['achievements'] as $i => $achieve) {
			$achievements[strtotime($achieve['obtained'])] = $i;
		}

		krsort($achievements);
		$achievements = array_slice($achievements, 0, $this->achievementRecentLimit);
		$this->tempdata['achievements_recent'] = $achievements;
	}
}
