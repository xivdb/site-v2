<?php

namespace XIVDB\Apps\Content;

class Enemy extends Content
{
    const TYPE = 'enemy';

    // All columns
    public static $main =
    [
        'id',
        'map',
        'position',
        'xyz',
        'placename',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'name_plural_ja',
        'name_plural_en',
        'name_plural_fr',
        'name_plural_de',
        'name_plural_cns',
        'lodestone_type',
        'lodestone_id',
        'connect_nonpop',
        'connect_area',
        'connect_instance',
        'connect_items',
        'patch',
        'connect_nonpop',
        'connect_area',
        'connect_instance',
        'connect_items'
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'lodestone_type',
        'lodestone_id',
        'patch',
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
        'patch' => 'Patch',
    ];

    public static $unset = [
        'instances',
        'items',
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
            ->from(ContentDB::ENEMY)
            ->addColumns([ ContentDB::ENEMY => array_merge(
                $this->isFlagged('extended') ? Enemy::$basic : Enemy::$main,
                Enemy::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::ENEMY))
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

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // stuff excluded from add<Content>
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // add zones
            $this->data['map_data'] = $this->findAppMapData('Monster', $this->id);

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

            // Add instances
            $this->data['instances'] = null;
            if ($this->hasColumn('connect_instance')) {
                $sql = $dbs->QueryBuilder->reset();
                $sql->select('*')->from(ContentDB::TO_ENEMY_INSTANCE)->where('enemy = :id')->bind('id', $this->id);

                // add chests if any found
                foreach($dbs->get()->all() as $res) {
                    $this->data['instances'][] = $this->addInstance($res['instance']);
                }
            }

            // Add item drops
            $this->data['items'] = null;
            if ($this->hasColumn('connect_items')) {
                $sql = $dbs->QueryBuilder->reset();
                $sql->select('*')->from(ContentDB::TO_ENEMY)->where('enemy = :id')->bind('id', $this->id);

                // add chests if any found
                foreach($dbs->get()->all() as $res) {
                    $this->data['items'][] = $this->addItem($res['item']);
                }

                $this->sksort($this->data['items'], 'level_item');
            }
        }
    }

    //
    // Set game data
    //
    public function setGameData()
    {
        $this->data['icon'] = SECURE . '/img/ui/enemy.png';

        if (isset($this->data['zones']) && $this->data['zones']) {
            foreach($this->data['zones'] as $i => $zone) {
                if (!isset($zone['placename']['id'])) {
                    unset($this->data['zones'][$i]);
                }
            }
        }
    }
}
