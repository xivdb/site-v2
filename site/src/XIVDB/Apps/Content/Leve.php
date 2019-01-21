<?php

namespace XIVDB\Apps\Content;

class Leve extends Content
{
    const TYPE = 'leve';

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
        'icon_issuer',
        'gil_reward',
        'exp_reward',
        'leve_image_text',
        'leve_client',
        'leve_assignment_type',
        'leve_reward_group',
        'leve_vfx',
        'leve_vfx_frame',
        'craft_leve',
        'xyz',
        'class_level',
        'classjob_category',
        'type',
        'banner',
        'time_limit',
        'placename',
        'icon_city_state',
        'data',
        'map',
        'position',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'class_level',
        'classjob_category',
        'placename',
        'leve_vfx',
        'leve_vfx_frame',
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
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'class_level',
    ];

    // (join) Assignment columns
    public static $assignment =
    [
        'name_{lang} as assignment_type_name',
        'icon as assignment_type_icon'
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'assignment_type_icon',
        'classjob_category',
        'icon_hq',
        'icon_map',
        'zones',
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
            ->from(ContentDB::LEVES)
            ->addColumns([ ContentDB::LEVES => array_merge(
                $this->isFlagged('extended') ? LEve::$basic : LEve::$main,
                LEve::$language)
            ])
            ->addColumns([ ContentDB::LEVES_ASSIGNMENT => LEve::$assignment ])
            ->join([ ContentDB::LEVES => 'leve_assignment_type' ], [ ContentDB::LEVES_ASSIGNMENT => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::LEVES))
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

        if (!$this->isFlagged('search'))
        {
            // Add placename
            $this->data['placename'] = $this->hasColumn('placename') ? $this->addPlacename($this->data['placename']) : null;

            // Add client
            $this->data['leve_client'] = $this->hasColumn('leve_client') ? $this->addLevesClient($this->data['leve_client']) : null;

            // Add leve assignment type
            $this->data['leve_assignment_type'] = $this->hasColumn('leve_assignment_type') ? $this->addLeveAssignmentType($this->data['leve_assignment_type']) : null;

            // Add VFX
            $this->data['leve_vfx'] = $this->hasColumn('leve_vfx') ? $this->addLeveVFX($this->data['leve_vfx']) : null;
            $this->data['leve_vfx_frame'] = $this->hasColumn('leve_vfx_frame') ? $this->addLeveVFX($this->data['leve_vfx_frame']) : null;
        }

        // Add on class job category
        $this->data['classjob_category'] = $this->hasColumn('classjob_category')
            ? $this->addClassJobCategory($this->data['classjob_category']) : null;

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // stuff excluded from add<Content>
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('search') && !$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // assignment type
            $this->data['leve_reward_group'] = $this->addLeveRewardGroup($this->data['leve_reward_group']);
            foreach($this->data['leve_reward_group'] as $i => $group) {
                $this->data['leve_reward_group'][$i]['group'] = $this->addLeveRewardItem($group['group']);
            }

            //
            // Re-array items
            //
            $this->data['items'] = [];
            $this->data['items_total'] = 0;
            if ($this->data['leve_reward_group'])
            {
                foreach($this->data['leve_reward_group'] as $i => $groupdata)
                {
                    if ($groupdata['group'])
                    {
                        foreach($groupdata['group'] as $itemdata) {
                            $this->data['items'][$itemdata['item']] = $this->addItem($itemdata['item']);
                        }
                    }
                }
            }

            unset($this->data['leve_reward_group']);

            //
            // sort items into "kind_name"
            //
            if ($this->data['items'])
            {
                $this->data['items_total'] = count($this->data['items']);

                $temp = [];
                foreach ($this->data['items'] as $item) {
                    if ($item['id'] == 1) {
                        $this->data['items_total']--;
                        continue;
                    }
                    $kind = $item['kind_name'];
                    $category = $item['category_name'];
                    $temp[$kind][$category][] = $item;
                }

                foreach ($temp as $kind => $categories) {
                    foreach ($categories as $category => $item) {
                        $this->sksort($temp[$kind][$category], 'level_item');
                    }
                }

                $this->data['items'] = $temp;
            }

            //
            // Split help onto multiple lines
            //
            if (isset($this->data['help'])) {
                $this->data['help'] = str_ireplace('. ', '.<br><br>', $this->data['help']);
            }
        }

        $this->setPositionData();
    }

    //
    // Set positional data for this fate
    //
    public function setPositionData()
    {
        // add zones
        $this->data['zones'] = $this->addContentMaps($this->cid, $this->id);
    }

    //
    // Set some game data
    //
    public function setGameData()
    {
        $this->data['icon'] = SECURE . '/img/game/071000/071241.png';
        $this->data['icon_map'] = SECURE . '/img/game_map_icons/leve.png';

        if (isset($this->data['assignment_type_icon'])) {
            $this->data['assignment_type_icon'] = SECURE . $this->iconize($this->data['assignment_type_icon']);
        }

        if (isset($this->data['icon_issuer'])) {
            $this->data['icon_issuer'] = SECURE . $this->iconize($this->data['icon_issuer']);
            $this->data['banner'] = $this->data['banner'] > 0 ? SECURE . $this->iconize($this->data['banner']) : $this->data['icon_issuer'];
            $this->data['icon_city_state'] = SECURE . $this->iconize($this->data['icon_city_state']);
        }

        if (isset($this->data['leve_vfx']['icon'])) {
            $this->data['leve_vfx']['icon'] = SECURE . $this->iconize($this->data['leve_vfx']['icon']);
        }

        if (isset($this->data['leve_vfx_frame']['icon'])) {
            $this->data['leve_vfx_frame']['icon'] = SECURE . $this->iconize($this->data['leve_vfx_frame']['icon']);
        }

        if (isset($this->data['leve_assignment_type']['icon'])) {
            $this->data['leve_assignment_type']['icon'] = SECURE . $this->iconize($this->data['leve_assignment_type']['icon']);
        }

        // Maps
        $this->data = $this->handleMaps($this->data);
    }
}
