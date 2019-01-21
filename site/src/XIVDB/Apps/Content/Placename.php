<?php

namespace XIVDB\Apps\Content;

class Placename extends Content
{
    const TYPE = 'placename';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_de',
        'name_fr',
        'name_cns',
        'region',
        'patch',
        'lodestone_id',
        'lodestone_type'
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'patch',
        'lodestone_id',
        'lodestone_type'
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
    ];

    // Search columns
    public static $search =
    [
        'id',
        'name_{lang} as name',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'region' => 'Has Region',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'enemies',
        'icon_hq',
        'instances',
        'map',
        'npcs',
        'quests',
        'region',
        'region_name',
    ];

    //
    // Get the content data
    //
    public function getContentData()
    {
        $dbs = $this->getModule('database');
        $sql = $dbs->QueryBuilder;

        // generate sql query
        $sql->select()
            ->from(ContentDB::PLACENAMES)
            ->addColumns([ ContentDB::PLACENAMES => array_merge(
                $this->isFlagged('extended') ? Placename::$basic : Placename::$main,
                Placename::$language)
            ])
            ->join([ ContentDB::PLACENAMES => 'region' ], [ ContentDB::PLACENAMES => 'id' ], 'region')
            ->where(sprintf('%s.id = :id', ContentDB::PLACENAMES))
            ->bind('id', $this->id)
            ->limit(0,1);

        // return
        $this->data = $dbs->get()->one();
        return $this;
    }

    //
    // tooltip data
    //
    public function tooltip()
    {
        return [
            'name' => $this->data['name'],
            'icon' => $this->data['icon'],
        ];
    }

    //
    // Manual gamedata modification
    //
    public function manual()
    {
        $dbs = $this->getModule('database');

        // add map
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::PLACENAMES_MAPS)
            ->where(['placename = :id', "folder != 'default/00'"], 'and')
            ->notempty('folder')->bind('id', $this->id);

        $this->data['maps'] = $dbs->get()->all();

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // stuff excluded from add<Content>
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            //
            // Set content from map markers
            //
            $this->data['enemies'] = [];
            $this->data['npcs'] = [];
            $this->data['gathering'] = [];
            $this->setContentFromMapMarkers($this->id);

            //
            // Add Instances
            //
            $this->data['instances'] = null;
            $dbs->QueryBuilder
                ->select('id')
                ->from(ContentDB::INSTANCES)
                ->where('placename = :id')
                ->bind('id', $this->id);

            foreach($dbs->get()->all() as $res) {
                $this->data['instances'][] = $this->addInstance($res['id']);
            }

            //
            // Add Quests
            //
            $this->data['quests'] = null;
            $dbs->QueryBuilder
                ->select('id')
                ->from(ContentDB::QUEST)
                ->where('placename = :id')
                ->bind('id', $this->id);

            foreach($dbs->get()->all() as $res) {
                $this->data['quests'][] = $this->addQuest($res['id']);
            }

            if (!empty($this->data['quests'])) {
                $this->sksort($this->data['quests'], 'class_level_1');
            }
        }
    }

    //
    // Set some game data
    //
    public function setGameData()
    {
        $this->data['icon'] = SECURE . '/img/ui/aether.png';

        // custom banners
        $banner = sprintf('/img/placenames/%s.png', $this->data['id']);
        if (file_exists(ROOT_WEB . $banner)) {
            $this->data['banner'] = $banner;
        }

        // if maps were added, handle them.
        if (isset($this->data['maps']) && $this->data['maps']) {
            $this->data['maps'] = $this->addMapUrls($this->data['name'], $this->data['maps']);
        }
    }
}
