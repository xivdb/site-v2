<?php

namespace XIVDB\Apps\Content;

class Characters extends Content
{
    const TYPE = 'character';

    // Order columns
    public static $order =
    [
        'lodestone_id' => 'ID',
        'name' => 'Name',
        'server' => 'Server',
        'achievements_score_reborn' => 'ARR 2.0+ Achievement Points',
        'achievements_score_legacy' => 'Legacy 1.0 Achievement Points',
        'data_last_changed' => 'Last Active',
        'last_updated' => 'Last Updated',
        'added' => 'Added',
    ];

    public static $replace = [
        'lodestone_id' => 'id',
    ];

    //
    // Get the content data
    //
    public function getContentData()
    {
        $dbs = $this->getModule('database');
        $sql = $dbs->QueryBuilder;

        // generate sql query
        $sql->select('*')
            ->from(ContentDB::CHARACTERS)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS))
            ->bind('id', $this->id)
            ->limit(0,1);

        // return
        $this->data = $dbs->get()->one();
        return $this;
    }

    public function getPrivacyData()
    {
        $dbs = $this->getModule('database');
        $sql = $dbs->QueryBuilder;

        $sql->select('*')
            ->from('characters_privacy')
            ->where('id = :id')
            ->bind('id', $this->id)
            ->limit(0,1);

        return $dbs->get()->one();
    }

    //
    // Get character data
    //
    public function getStorageData()
    {
        $dbs = $this->getModule('database');
        $sql = $dbs->QueryBuilder;

        // generate sql query
        $sql->select('*')
            ->from(ContentDB::CHARACTERS_DATA)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_DATA))
            ->bind('id', $this->id)
            ->limit(0,1);

        // return
        return $dbs->get()->one();
    }

    //
    // Get characters events data
    //
    public function getEventsData()
    {
        $dbs = $this->getModule('database');

        $data = [
            'exp' => [],
            'lvs' => [],
        ];

        // get exp events
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_EVENTS_EXP_NEW)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_EVENTS_EXP_NEW))
            ->bind('id', $this->id);

        $data['exp'] = $dbs->get()->all();

        // get lv events
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_EVENTS_LVS_NEW)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_EVENTS_LVS_NEW))
            ->bind('id', $this->id);

        $data['lvs'] = $dbs->get()->all();

        return $data;
    }

    //
    // Get tracking data
    //
    public function getTrackingData()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_EVENTS_TRACKING)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_EVENTS_TRACKING))
            ->bind('id', $this->id);

        $data = $dbs->get()->all();

        return $data;
    }

    //
    // Get gearsets data
    //
    public function getGearsetsData()
    {
        // get more
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_GEARSETS)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_GEARSETS))
            ->bind('id', $this->id);

        $data = [];
        foreach($dbs->get()->all() as $obj) {
            $obj['stats'] = json_decode($obj['stats'], true);
            $data[$obj['classjob_id']] = $obj;
        }

        return $data;
    }

    //
    // Get achievements list data
    //
    public function getAchievementsListData()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_ACHIEVEMENTS_LIST)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_ACHIEVEMENTS_LIST))
            ->bind('id', $this->id);

        return $dbs->get()->all();
    }

    //
    // Get achievements possible data
    //
    public function getAchievementsPossibleData()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_ACHIEVEMENTS_POSSIBLE)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_ACHIEVEMENTS_POSSIBLE))
            ->bind('id', $this->id);

        $data = $dbs->get()->one();
        $data['possible'] = json_decode($data['possible']);

        return $data;
    }

    public function getAchievementsObtained()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CHARACTERS_ACHIEVEMENTS_LIST)
            ->where(sprintf('%s.lodestone_id = :id', ContentDB::CHARACTERS_ACHIEVEMENTS_LIST))
            ->bind('id', $this->id);

        $arr = [];
        foreach($dbs->get()->all() as $entry) {
            $arr[] = [
                'achievement_id' => $entry['achievement_id'],
                'obtained' => $entry['obtained'],
            ];
        }

        return $arr;
    }

    //
    // Manual gamedata modification
    //
    public function manual()
    {
        $this->attachWebsiteUrls();

        // modify some images
		$this->data['icon'] = $this->data['icon'] .'?t='. date('YzG');
    }
}
