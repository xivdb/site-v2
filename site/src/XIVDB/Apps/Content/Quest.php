<?php

namespace XIVDB\Apps\Content;

class Quest extends Content
{
    const TYPE = 'quest';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'placename',
        'header',
        'header_special',
        'journal_genre',
        'journal_category',
        'gil_reward',
        'exp_factor',
        'exp_reward',
        'action_reward',
        'npc_start',
        'npc_end',
        'classjob_category_1',
        'classjob_category_2',
        'class_level_1',
        'class_level_2',
        'classjob_1',
        'classjob_2',
        'classjob_unlock',
        'classjob_required',
        'quest_level_offset',
        'beast_tribe',
        'beast_reputation_rank',
        'webtype',
        'item_array_type',
        'lodestone_type',
        'lodestone_id',
        'sort_key',
        'data_file',
        'previous_quest_join',
        'quest_lock_join',
        'grand_company',
        'grand_company_rank',
        'instance_content_join',
        'bell_start',
        'bell_end',
        'is_house_required',
        'is_repeatable',
        'mount_required',
        'repeat_interval_type',
        'gc_seals',
        'item_reward_type',
        'emote_reward',
        'instance_content_unlock',
        'tomestone_reward',
        'tomestone_count_reward',
        'reputation_reward',
        'connect_classjob',
        'connect_classjob_category',
        'connect_npc',
        'connect_post',
        'connect_pre',
        'connect_reward',
        'patch',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'header',
        'header_special',
        'exp_reward',
        'class_level_1',
        'class_level_2',
        'lodestone_type',
        'lodestone_id',
        'patch',
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
        'header_special',
        'journal_genre',

        'classjob_category_1',
        'classjob_category_2',
        'class_level_1',
        'class_level_2',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'journal_genre' => 'Journal',
        'class_level_1' => 'Level',
        'class_level_2' => 'Level (Sub cCass)',
        'gil_reward' => 'Gil Reward',
        'exp_reward' => 'EXP Reward',
        'gc_seals' => 'Grand Company Seals',
        'tomestone_count_reward' => 'Tomestones',
        'reputation_reward' => 'Reputation Points',
        'beast_reputation_rank' => 'Beast Tribe Rank',
        'grand_company_rank' => 'Grand Company Rank',
        'journal_genre' => 'Journal',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'category_name',
        'header_special',
        'npc_end',
        'npc_start',
        'npcs',
        'placename',
        'post_quests',
        'pre_quests',
        'rewards',
        'text',
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
            ->from(ContentDB::QUEST)
            ->addColumns([ ContentDB::QUEST => array_merge(
                $this->isFlagged('extended') ? Quest::$basic : Quest::$main,
                Quest::$language)
            ])
            ->addColumns([ ContentDB::JOURNAL_GENRE => [ 'id as genre', 'name_{lang} as genre_name', 'icon as genre_icon' ] ])
            ->addColumns([ ContentDB::JOURNAL_CATEGORY => [ 'id as category', 'name_{lang} as category_name' ] ])
            ->addColumns([ ContentDB::BEAST_TRIBE => [ 'name_{lang} as beast_tribe_name' ] ])
            ->addColumns([ ContentDB::NPC => [ 'id as npc_id', 'name_{lang} as npc_name' ] ])
            ->addColumns([ 'class_1' => [ 'name_{lang} as class_name_1' ] ])
            ->addColumns([ 'class_2' => [ 'name_{lang} as class_name_2' ] ])
            ->addColumns([ 'class_1_category' => [ 'name_{lang} as class_category_1' ] ])
            ->addColumns([ 'class_2_category' => [ 'name_{lang} as class_category_2' ] ])
            ->join([ ContentDB::QUEST => 'journal_genre' ], [ ContentDB::JOURNAL_GENRE => 'id' ])
            ->join([ ContentDB::JOURNAL_GENRE => 'journal_category' ], [ ContentDB::JOURNAL_CATEGORY => 'id' ])
            ->join([ ContentDB::QUEST => 'beast_tribe' ], [ ContentDB::BEAST_TRIBE => 'id' ])
            ->join([ ContentDB::QUEST => 'npc_start' ], [ ContentDB::NPC => 'id' ])
            ->join([ ContentDB::QUEST => 'classjob_1' ], [ ContentDB::CLASSJOB => 'id' ], 'class_1')
            ->join([ ContentDB::QUEST => 'classjob_2' ], [ ContentDB::CLASSJOB => 'id' ], 'class_2')
            ->join([ ContentDB::QUEST => 'classjob_category_1' ], [ ContentDB::CLASSJOB_CATEGORY => 'id' ], 'class_1_category')
            ->join([ ContentDB::QUEST => 'classjob_category_2' ], [ ContentDB::CLASSJOB_CATEGORY => 'id' ], 'class_2_category')

            ->where(sprintf('%s.id = :id', ContentDB::QUEST))
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

        // Add placename
        $this->data['placename'] = $this->hasColumn('placename')
            ? $this->addPlacename($this->data['placename'])
            : null;

        // Add start and end npcs
        $this->data['npc_start'] = $this->hasColumn('npc_start')
            ? $this->addNPC($this->data['npc_start'])
            : null;

        $this->data['npc_end'] = $this->hasColumn('npc_end')
            ? $this->addNPC($this->data['npc_end'])
            : null;

        // Add class job categories
        $this->data['classjob_category_1'] = $this->hasColumn('classjob_category_1')
            ? $this->addClassJobCategory($this->data['classjob_category_1'])
            : null;

        $this->data['classjob_category_2'] = $this->hasColumn('classjob_category_2')
            ? $this->addClassJobCategory($this->data['classjob_category_2'])
            : null;

        // Stuff to include when not extended
        if (!$this->isFlagged('extended'))
        {

        }

        // Stuff to include when not extended and not a tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            //
            // Add NPC's involved
            //
            $this->data['npcs'] = null;
            if ($this->hasColumn('connect_npc')) {
                $sql = $dbs->QueryBuilder->reset();
                $sql->select('npc')
                    ->from(ContentDB::TO_NPC_QUESTS)
                    ->where('quest = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    $this->data['npcs'][] = $this->addNPC($res['npc']);
                }
            }

            //
            // Add Post-Quests
            //
            $this->data['post_quests'] = null;
            if ($this->hasColumn('connect_post')) {
                $sql = $dbs->QueryBuilder->reset();
                $sql->select('quest_post')
                    ->from(ContentDB::QUEST_POST)
                    ->where('quest = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    if ($postquest = $this->addQuest($res['quest_post'])) {
                        $this->data['post_quests'][] = $postquest;
                    }
                }
            }

            //
            // Add Pre-Quests
            //
            $this->data['pre_quests'] = null;
            if ($this->hasColumn('connect_pre')) {
                $sql = $dbs->QueryBuilder->reset();
                $sql->select('quest_pre')
                    ->from(ContentDB::QUEST_PRE)
                    ->where('quest = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    if ($prequest = $this->addQuest($res['quest_pre'])) {
                        $this->data['pre_quests'][] = $prequest;
                    }
                }
            }

            //
            // Add Rewards
            //
            $this->data['items'] = null;
            $this->data['items_total'] = 0;
            if ($this->hasColumn('connect_reward')) {
                $sql = $dbs->QueryBuilder->reset();
                $sql->select('quantity, item')
                    ->from(ContentDB::QUEST_REWARDS)
                    ->where('quest = :id')
                    ->bind('id', $this->id);

                foreach ($dbs->get()->all() as $res) {
                    $quantity = $res['quantity'] > 0 ? $res['quantity'] : 1;
                    $item = $this->addItem($res['item']);
                    $item['_quantity'] = $quantity;

                    $this->data['items'][] = $item;
                }

                //
                // sort items into "kind_name"
                //
                if ($this->data['items'])
                {
                    $this->data['items_total'] = count($this->data['items']);

                    $temp = [];
                    foreach ($this->data['items'] as $item) {
                        if (!isset($item['id']) || $item['id'] == 1 || !isset($item['kind_name'])) {
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
            }

            //
            // Add text data
            //
            $sql = $dbs->QueryBuilder->reset();
            $sql->select(['id', 'quest', 'define', 'text_{lang} as text', 'patch'])
                ->from(ContentDB::TO_QUESTS_TEXT)
                ->where('quest = :id')->bind('id', $this->id)
                ->order('id', 'asc');

            $this->data['text'] = $this->setTextData($dbs->get()->all());
        }
    }

    //
    // Handle quest text data
    //
    public function setTextData($steps)
    {
        $colors = [];
        $temp = [
            'text' => [],
            'scene' => [],
            'journal' => [],
            'todo' => [],
        ];

        foreach($steps as $i => $step)
        {
            $placement = $step['id'];
            $define = explode('_', $step['define']);

            // get type
            $type = strtolower($define[0]);
            $npc = ucwords(strtolower($define[3]));
            $npc = preg_replace('/[0-9]+/', null, $npc);

            // if scene
            if ($npc == 'Scene')
            {
                $type = 'scene';
                $npc = false;
                $placement = $define[7];
            }
            else if ($npc == 'Seq')
            {
                $type = 'journal';
                $npc = false;
                $placement = intval($define[4]);

                $step['text'] = str_ireplace('...', "...\n\n", $step['text']);
            }
            else if ($npc == 'Todo')
            {
                $type = 'todo';
                $npc = false;
                $placement = intval($define[4]);
            }
            else if ($npc == 'Pop')
            {
                $type = 'pop';
                $npc = false;
            }
            else
            {
                if (!isset($define[5]))
                {
                    $type = strtolower($define[3]);
                    $npc = false;

                    if ($type == 'qib')
                    {
                        $todoId = intval(filter_var($define[4], FILTER_SANITIZE_NUMBER_INT));
                        $temp['todo'][$todoId]['qib'] = $step;
                        continue;
                    }
                }
                else
                {
                    $placement = intval($define[5]);
                }
            }

            $step['type'] = strtolower($type);
            $step['npc'] = $npc;

            if (!isset($colors[$step['npc']])) {
                $colors[$step['npc']] = $this->getRandomColor(125,255);
            }

            $step['color'] = $colors[$step['npc']];

            if (isset($temp[$type][$placement])) {
                $temp[$type][$placement] = array_merge($temp[$type][$placement], $step);
            } else {
                $temp[$type][$placement] = $step;
            }
        }

        ksort($temp['text']);
        ksort($temp['scene']);
        ksort($temp['journal']);
        ksort($temp['todo']);

        return $temp;
    }

    //
    // Set game data
    //
    public function setGameData()
    {
        $this->data['icon'] = '/img/game/071000/071221.png';

        // set icon based on genre
        if (isset($this->data['genre_icon'])) {
            $ids = [
                '61411' => '71221',
                '61412' => '71201',
                '61413' => '71222',
                '61414' => '71281',
                '61415' => '60552',
                '61416' => '61436',

                // grand companies
                '61401' => '62951', // limsa
                '61402' => '62952', // grid
                '61403' => '62953', // uldah
            ];

            $this->data['genre_icon'] = isset($ids[$this->data['genre_icon']])
                ? $ids[$this->data['genre_icon']] : $this->data['genre_icon'];

            $this->data['icon'] = SECURE . $this->iconize($this->data['genre_icon']);
        }

        // set icon type
        if (!$this->data['icon'] && isset($this->data['category']))
        {
            if (in_array($this->data['category'], [1,2])) {
                $this->data['icon'] =  SECURE . '/img/ui/main-scenerio.png';
            }

            if (in_array($this->data['category'], [24,25,26,27,28,29,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49])) {
                $this->data['icon'] = SECURE . '/img/ui/guildleves.png';
            }
        }

        // set header
        if (isset($this->data['header']) && $this->data['header']) {
            $this->data['header'] = $this->iconize($this->data['header']);

            if (!$this->data['header']) {
                $this->data['header'] = null;
            } else {
                $this->data['header'] = SECURE . $this->data['header'];
            }
        }

        // set icon if its special
        if (isset($this->data['header_special']) && $this->data['header_special']) {
            $this->data['icon'] = SECURE . $this->iconize($this->data['header_special']);
        }

        // add npc url
        if (isset($this->data['npc_name']) && $this->data['npc_name']) {
            $this->data['npc_url'] = $this->url('npc', $this->data['npc_id'], $this->data['npc_name']);
        }
    }
}
