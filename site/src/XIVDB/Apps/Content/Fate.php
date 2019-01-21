<?php

namespace XIVDB\Apps\Content;

class Fate extends Content
{
    const TYPE = 'fate';

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
        'xyz',
        'class_level',
        'class_level_max',
        'icon',
        'icon_small',
        'event_item',
        'objective',
        'map',
        'position',
        'placename',
        'patch',
        'lodestone_id',
        'lodestone_type',
        'objective_0_ja',
        'objective_0_en',
        'objective_0_fr',
        'objective_0_de',
        'objective_0_cns',
        'objective_1_ja',
        'objective_1_en',
        'objective_1_fr',
        'objective_1_de',
        'objective_1_cns',
        'objective_2_ja',
        'objective_2_en',
        'objective_2_fr',
        'objective_2_de',
        'objective_2_cns',
        'objective_3_ja',
        'objective_3_en',
        'objective_3_fr',
        'objective_3_de',
        'objective_3_cns'
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'class_level',
        'icon',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
        'help_{lang} as help',
        'objective_0_{lang} as objective_0',
        'objective_1_{lang} as objective_1',
        'objective_2_{lang} as objective_2',
        'objective_3_{lang} as objective_3',
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
        'class_level',
        'class_level_max',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'class_level' => 'Level',
        'class_level_max' => 'Level (Max)',
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
            ->from(ContentDB::FATES)
            ->addColumns([ ContentDB::FATES => array_merge(
                $this->isFlagged('extended') ? Fate::$basic : Fate::$main,
                Fate::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::FATES))
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
        if (!$this->isFlagged('search') && !$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // event item
            $this->data['event_item'] = $this->data['event_item'] ? $this->addEventItem($this->data['event_item']) : null;
        }

        if (!$this->isFlagged('search'))
        {
            // add zones
            $this->data['map'] = null;
            $map = $this->addContentMaps($this->cid, $this->id);
            if ($map) {
                $this->data['map'] = $map[0];
            }

            //
            // Split help onto multiple lines
            //
            if (isset($this->data['help'])) {
                $this->data['help'] = str_ireplace('. ', '.<br><br>', $this->data['help']);
            }
        }
    }

    //
    // Set some game data
    //
    public function setGameData()
    {
        // event item
        if (isset($this->data['event_item']['icon'])) {
            $this->data['event_item']['icon'] = $this->iconize($this->data['event_item']['icon']);
            $this->data['event_item']['color'] = ContentDB::$rarity[$this->data['event_item']['rarity']];
        }

        // maps
        $this->data = $this->handleMaps($this->data);

        // set icons
        $icons = [
            '0' => '060093.png',
            '60501' => '060501.png',
            '60502' => '060502.png',
            '60503' => '060503.png',
            '60504' => '060504.png',
            '60505' => '060505.png',
            '60958' => '060958.png',
        ];

        $this->data['icon'] = SECURE . '/img/game_local/fates/'. $icons[$this->data['icon']];
        if (isset($this->data['icon_small'])) {
            $this->data['icon_small'] = SECURE . $this->iconize($this->data['icon_small']);
        }
    }
}
