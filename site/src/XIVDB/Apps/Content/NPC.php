<?php

namespace XIVDB\Apps\Content;

class NPC extends Content
{
    const TYPE = 'npc';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'title_ja',
        'title_en',
        'title_fr',
        'title_de',
        'title_cns',
        'name_plural_ja',
        'name_plural_en',
        'name_plural_fr',
        'name_plural_de',
        'name_plural_cns',
        'lodestone_type',
        'lodestone_id',
        'has_shop',
        'has_shop_conditional',
        'has_quest',
        'is_dev',
        'map',
        'position',
        'placename',
        'patch',
        'connect_area',
        'connect_quest',
        'connect_region',
        'connect_shop',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'placename',
        'lodestone_type',
        'lodestone_id',
        'patch',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
        'title_{lang} as title',
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
        'title_{lang} as title',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'placename',
        'quests',
        'shops',
        'title',
        'zones',
        'icon_hq',
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
            ->from(ContentDB::NPC)
            ->addColumns([ ContentDB::NPC => array_merge(
                $this->isFlagged('extended') ? NPC::$basic : NPC::$main,
                NPC::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::NPC))
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

        // add placename
        $this->data['placename'] = $this->hasColumn('placename') ? $this->addPlacename($this->data['placename']) : null;

        // add zones
        $this->data['map_data'] = $this->findAppMapData('NPC', $this->id);

        // add a primary map point, the first one.
        $this->data['map_primary'] = null;
        if ($this->data['map_data']) {
            $primaryMap = $this->data['map_data']['maps'][0];
            $primaryPoint = $this->data['map_data']['points'][0];

            $this->data['map_primary'] = [
                'placename' => $this->addPlacename($primaryMap['placename_id']),
                'position'  => [
                    'x' => $primaryPoint['app_position']['ingame']['x'],
                    'y' => $primaryPoint['app_position']['ingame']['y']
                ],
            ];
        }

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // stuff excluded from add<Content>
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // Add quests
            $this->data['quests'] = null;
            if ($this->hasColumn('connect_quest')) {
                $dbs->QueryBuilder->select('*')->from(ContentDB::TO_NPC_QUESTS)->where('npc = :id')->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    $this->data['quests'][] = $this->addQuest($res['quest']);
                }
            }

            // Add quests
            $this->data['shops'] = null;
            if ($this->hasColumn('connect_shop')) {
                $dbs->QueryBuilder->select('*')->from(ContentDB::TO_NPC_SHOP)->where('npc = :id')->bind('id', $this->id);

                // get shops
                foreach($dbs->get()->all() as $res) {
                    $shop = $this->addShop($res['shop']);
                    $shop['items'] = [];

                    // get the items for the shop
                    $dbs->QueryBuilder->select('*')->from(ContentDB::TO_SHOP)->where('shop = :id')->bind('id', $shop['id']);
                    foreach($dbs->get()->all() as $res) {
                        $shop['items'][] = $this->addItem($res['item']);
                    }

                    // set shop
                    $this->data['shops'][] = $shop;
                }
            }
        }
    }

    //
    // Set some game data
    //
    public function setGameData()
    {
        $this->data['icon'] = SECURE . '/img/ui/npc.png';
    }
}
