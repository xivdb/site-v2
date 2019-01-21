<?php

namespace XIVDB\Apps\Content;

class Weather extends Content
{
    const TYPE = 'weather';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'type_ja',
        'type_en',
        'type_fr',
        'type_de',
        'type_cns',
        'icon',
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
        'type_{lang} as type',
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
        'type_{lang} as type',
        'icon',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'patch' => 'Patch',
    ];

    public static $unset = [
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
            ->from(ContentDB::WEATHER)
            ->addColumns([ ContentDB::WEATHER => array_merge(
                $this->isFlagged('extended') ? Weather::$basic : Weather::$main,
                Weather::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::WEATHER))
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
}
