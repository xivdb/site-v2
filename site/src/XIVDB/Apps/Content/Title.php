<?php

namespace XIVDB\Apps\Content;

class Title extends Content
{
    const TYPE = 'title';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'name_female_ja',
        'name_female_en',
        'name_female_fr',
        'name_female_de',
        'name_female_cns',
        'is_prefix',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
        'name_female_{lang} as name_female',
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
        'name_female_{lang} as name_female',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name (Male)',
        'name_female_{lang}' => 'Name (Female)',
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
            ->from(ContentDB::TITLES)
            ->addColumns([ ContentDB::TITLES => array_merge(
                $this->isFlagged('extended') ? Title::$basic : Title::$main,
                Title::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::TITLES))
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
            $dbs->QueryBuilder->select('id')->from(ContentDB::ACHIEVEMENTS)->where('title = :id')->bind('id', $this->id);

            foreach($dbs->get()->all() as $res) {
                $this->data['achievements'][] = $this->addAchievement($res['id']);
            }
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // stuff excluded from add<Content>
            // stuff excldued from Tooltips
        }
    }

    public function setGameData()
    {
        $this->data['icon'] = SECURE . '/img/ui/lodestone/title.png';
    }
}
