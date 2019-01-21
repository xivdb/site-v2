<?php

namespace XIVDB\Apps\Content;

class Gathering extends Content
{
    const TYPE = 'gathering';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'item',
        'gathering_type',
        'level',
        'level_view',
        'level_diff',
        'is_hidden',
        'gathering_notebook_list',
        'gathering_item_number',
        'lodestone_type',
        'lodestone_id',
        'patch'
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'item',
        'level',
        'level_view',
        'level_diff',
        'lodestone_type',
        'lodestone_id',
        'patch'
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
        'item',
        'level',
        'level_view',
        'level_diff',
    ];

    // (join) Item columns
    public static $item =
    [
        'level_equip as item_level_equip',
        'level_item as item_level_item',
        'rarity as rarity',
        'icon as icon',
        'icon_lodestone as icon_lodestone',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'level' => 'Level',
        'level_view' => 'Difficulty Level',
        'level_diff' => 'Difficulty Rating',
        'is_hidden' => 'Is Hidden',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'icon_lodestone',
        'item',
        'nodes',
        'rarity',
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
            ->from(ContentDB::GATHERING)
            ->addColumns([ ContentDB::GATHERING => array_merge(
                $this->isFlagged('extended') ? Gathering::$basic : Gathering::$main,
                Gathering::$language)
            ])
            ->addColumns([ ContentDB::ITEMS => Gathering::$item ])
            ->addColumns([ ContentDB::GATHERING_TYPE => [ 'name_{lang} as type_name' ] ])
            ->addColumns([ ContentDB::PLACENAMES => [ 'name_{lang} as placename' ] ])
            ->join([ ContentDB::GATHERING => 'gathering_type' ], [ ContentDB::GATHERING_TYPE => 'id' ])
            ->join([ ContentDB::GATHERING => 'id' ], [ ContentDB::GATHERING_NODES => 'gathering' ])
            ->join([ ContentDB::GATHERING => 'item' ], [ ContentDB::ITEMS => 'id' ])
            ->join([ ContentDB::GATHERING_NODES => 'placename' ], [ ContentDB::PLACENAMES => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::GATHERING))
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

        // Add repair item
        $this->data['item'] = $this->hasColumn('item') ? $this->addItem($this->data['item']) : null;

        // Add nodes
        $this->data['nodes'] = null;
        $sql = $dbs->QueryBuilder->reset();
        $sql->select('*')->from(ContentDB::GATHERING_NODES)->where('gathering = :id')->bind('id', $this->id);
        foreach($dbs->get()->all() as $res)
        {
            $zone = $this->addPlacename($res['zone']);
            $zone['level'] = $res['level'];
            $this->data['nodes'][$res['level'] .'_'. $res['zone']] = $zone;
        }

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // stuff excluded from add<Content>
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {

        }

        // extras
        $this->setGameData();
    }

    //
    // General game data
    //
    public function setGameData()
    {
        if (isset($this->data['item']['icon'])) {
            $this->data['icon'] = $this->data['item']['icon'];
            $this->data['color'] = isset($this->data['item']['rarity']) ? ContentDB::$rarity[$this->data['item']['rarity']] : null;
        } else if (isset($this->data['icon'])) {
            $this->data = $this->icon($this->data);
            $this->data['color'] = isset($this->data['rarity']) ? ContentDB::$rarity[$this->data['rarity']] : null;
        }

        unset($this->data['icon_hq']);

        $this->data['stars'] = $this->data['level_diff'];
        $this->data['stars_html'] = '';

        if ($this->data['stars'] > 0) {
            for ($i=0; $i < $this->data['stars']; $i++) {
                $this->data['stars_html'] .= '&#9733;';
            }
        }
    }
}
