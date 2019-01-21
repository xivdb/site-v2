<?php

namespace XIVDB\Apps\Content;

class Status extends Content
{
    const TYPE = 'status';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'help_ja',
        'help_en',
        'help_fr',
        'help_de',
        'help_cns',
        'icon',
        'is_company_status',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'icon',
        'is_company_status',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
        'help_{lang} as help',
    ];

    // Search columns
    public static $search =
    [
        'id',
        'name_{lang} as name',
        'icon',
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
            ->from(ContentDB::STATUS)
            ->addColumns([ ContentDB::STATUS => array_merge(
                $this->isFlagged('extended') ? Status::$basic : Status::$main,
                Status::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::STATUS))
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
