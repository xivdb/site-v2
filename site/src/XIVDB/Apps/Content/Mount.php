<?php

namespace XIVDB\Apps\Content;

class Mount extends Content
{
    const TYPE = 'mount';

    // All columns
    public static $main =
    [
        'id',
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
        'name_plural_{lang} as name_plural',
        'summon_ja',
        'summon_en',
        'summon_fr',
        'summon_de',
        'summon_cns',
        'summon_{lang} as summon',
        'info1_ja',
        'info1_en',
        'info1_fr',
        'info1_de',
        'info1_cns',
        'info1_{lang} as info1',
        'info2_ja',
        'info2_en',
        'info2_fr',
        'info2_de',
        'info2_cns',
        'info2_{lang} as info2',
        'icon',
        'can_fly',
        'can_fly_extra',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'icon',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
        'info1_{lang} as info',
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
        'can_fly',
        'can_fly_extra',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'icon2',
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
            ->from(ContentDB::MOUNTS)
            ->addColumns([ ContentDB::MOUNTS => array_merge(
                $this->isFlagged('extended') ? Mount::$basic : Mount::$main,
                Mount::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::MOUNTS))
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
            // stuff excluded from add<Content>
            // stuff excldued from Tooltips
        }
    }

    //
    // Set some game data
    //
    public function setGameData()
    {
        // setup both icons
        $this->data['icon2'] = $this->data['icon'];

        $rep = [
            '/004' => '/068',
            '/008' => '/077',
        ];

        $this->data['icon'] = str_ireplace(array_keys($rep), $rep, $this->data['icon']);
    }

}
