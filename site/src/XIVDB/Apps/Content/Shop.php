<?php

namespace XIVDB\Apps\Content;

class Shop extends Content
{
    const TYPE = 'shop';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'beast_tribe',
        'beast_reputation_rank',
        'icon',
        'quest',
        'patch',
        'lodestone_id',
        'lodestone_type'
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'icon',
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
        'icon',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'patch' => 'Patch',
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
            ->from(ContentDB::SHOP)
            ->addColumns([ ContentDB::SHOP => array_merge(
                $this->isFlagged('extended') ? Shop::$basic : Shop::$main,
                Shop::$language)
            ])
            ->addColumns([ ContentDB::BEAST_TRIBE => [ 'name_{lang} as beast_tribe_name' ] ])
            ->addColumns([ ContentDB::NPC => [ 'id as npc_id' ] ])
            ->join([ ContentDB::SHOP => 'beast_tribe' ], [ ContentDB::BEAST_TRIBE => 'id' ])
            ->join([ ContentDB::SHOP => 'id' ], [ ContentDB::TO_NPC_SHOP => 'shop' ])
            ->join([ ContentDB::TO_NPC_SHOP => 'npc' ], [ ContentDB::NPC => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::SHOP))
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
    // Attach linked content
    //
    public function extended()
    {
        $dbs = $this->getModule('database');

        // Add NPC
        $this->data['npc'] = $this->addNPC($this->data['npc_id']);
    }

    //
    // Manual gamedata modification
    //
    public function manual()
    {
        $dbs = $this->getModule('database');

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // stuff excluded from add<Content>
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            $dbs->QueryBuilder->select('item')->from(ContentDB::TO_SHOP)->where('shop = :id')->bind('id', $this->id);
            foreach($dbs->get()->all() as $res) {
                $this->data['items'][] = $this->addItem($res['item']);
            }
        }
    }

    //
    // Set game data
    //
    public function setGameData()
    {
         $this->data['icon'] = SECURE . '/img/ui/061641.png';
    }
}
