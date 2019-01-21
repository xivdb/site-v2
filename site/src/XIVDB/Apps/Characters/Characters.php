<?php

namespace XIVDB\Apps\Characters;

use XIVDB\Apps\Content\Characters as ContentCharacters;

class Characters extends \XIVDB\Apps\AppHandler
{
	use Member;
	use DataMisc;
	use DataAchievements;
	use DataClassJobs;
	use DataGear;
	use DataEvents;
	use DataTimeline;
	use DataMinionsMounts;
	use DataStatistics;

	const TYPE = 'character';

	private $chardata;
	private $tempdata;
	private $content;

	public $blacklisted = false;

	function __construct()
	{
		$this->content = $this->getModule('content');
	}

	//
	// Process a character
	//
	public function process($id)
	{
		$redis = $this->getRedis();
		$key = 'character_content_'. $id;

		$cc = new ContentCharacters();
		$cc->setId($id);

		$user = $this->getUser();
		$ownsCharacter = false;
		$isHidden = false;

		if ($user && $user->characters && isset($user->characters[$id]) || $user && $user->isModerator()) {
		    $ownsCharacter = true;
        }

		// check privacy
        $privacy = $cc->getPrivacyData();
		if ($privacy && $privacy['hide_profile']) {
		    $isHidden = true;
        }

        if (!$ownsCharacter && $isHidden) {
            $this->blacklisted = true;
            return false;
        }

		$this->chardata = $cc->getContentData()->getData();

		if (!$this->chardata) {
			return false;
		}


		if (CACHE_CHARACTERS && $data = $redis->get($key)) {
			$this->tempdata = $data;
		} else {
			// request more from $cc
			$this->tempdata['events'] = $cc->getEventsData();
			$this->tempdata['tracking'] = $cc->getTrackingData();
			$this->tempdata['gearsets'] = $cc->getGearsetsData();
			$this->tempdata['achievements'] = $cc->getAchievementsListData();
			$this->tempdata['achievements_possible'] = $cc->getAchievementsPossibleData();
			$this->tempdata['achievements_obtained'] = $cc->getAchievementsObtained();
			$this->tempdata['storage_data'] = $cc->getStorageData();

			if (CACHE_CHARACTERS) {
				$redis->set($key, $this->tempdata, CACHE_GAME_CONTENT_CHARACTERS);
			}
		}

		if (!$this->tempdata['storage_data']['data']) {
			return false;
		}

		// process data
		$this->handleData();
		$this->handleMember();
		$this->handleMinionsMounts();
		$this->handleAchievements();
		$this->handleEvents();
		$this->handleTimeline();
		$this->handleClassJobs();
		$this->handleGear();
		$this->handleStatistics();

		$this->tempdata['is_hidden'] = $isHidden;

		//show($this->tempdata);
		//die;

		return true;
	}

    /**
     * @return mixed
     */
	public function getCharData()
	{
		return $this->chardata;
	}

    /**
     * @return mixed
     */
	public function getTempData()
	{
		return $this->tempdata;
	}
}
