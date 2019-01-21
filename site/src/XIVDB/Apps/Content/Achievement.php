<?php

namespace XIVDB\Apps\Content;

class Achievement extends Content
{
    const TYPE = 'achievement';

    // All columns
    public static $main =
    [
        'id',
        'achievement_category',
        'achievement_kind',
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
        'points',
        'icon',
        'item',
        'title',
        'priority',
        'order',
        'type',
        'lodestone_type',
        'lodestone_id',
        'connect_post',
        'connect_pre',
        'patch',
        'connect_post',
        'connect_pre',
        'connect_quest',
        'requirement_1',
        'requirement_2',
        'requirement_3',
        'requirement_4',
        'requirement_5',
        'requirement_6',
        'requirement_7',
        'requirement_8',
        'requirement_9',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'points',
        'icon',
        'lodestone_type',
        'lodestone_id',
        'patch',
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
        'achievement_category',
        'achievement_kind',
        'icon',
        'points',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'points' => 'Points',
        'achievement_category' => 'Category',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'achievement_category',
        'icon_hq',
    ];

    //
    // The different types of requirements for
    // completing an achievement.
    //
    // They link to translated types
    //
    protected $types =
    [
        0 => 804, // 'legacy',
        1 => 805, // 'activity', // kill x, get x gil, etc.
        2 => 397, // 'achievement',
        3 => 384, // 'class', // class level to get to
        4 => 438, // 'materia',
        5 => 806, // 'adventuring', - could link by achievement.
        6 => 807, // 'pre', // quest is_or = 0
        7 => 399, // 'hunting log',
        8 => 808, // 'discover',
        9 => 809, // 'pre', // quest is_or = 1
        10 => 467, // 'companion', // chocobo rank
        11 => 810, // 'grand company',
        12 => 811, // 'pvp', // particpate
        13 => 812, // 'pvp', // win
        14 => 813, // 'trial',
        15 => 814, // 'beast tribe',
        // 16 => 'doesnt exist ???',
        17 => 815, // 'frontline', // Participate
        18 => 816, // victorious, 'frontline',
        19 => 817, // 'frontline',
        20 => 818, // 'aether attune',
        21 => 446, // 'minions',
        // 22 => 'doesnt eixt ???',
        23 => 819, // 'vermillion challenge .. gold saucer',
        24 => 820, // getting a Hyperconductive or Anima weapon
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
            ->from(ContentDB::ACHIEVEMENTS)
            ->addColumns([ ContentDB::ACHIEVEMENTS => array_merge(
                $this->isFlagged('extended') ? Achievement::$basic : Achievement::$main,
                Achievement::$language)
            ])
            ->addColumns([ ContentDB::ACHIEVEMENTS_CATEGORY => [ 'id as category_id', 'name_{lang} as category_name' ] ])
            ->addColumns([ ContentDB::ACHIEVEMENTS_KIND => [ 'id as kind_id', 'name_{lang} as kind_name' ] ])

            ->join([ ContentDB::ACHIEVEMENTS => 'achievement_category' ], [ ContentDB::ACHIEVEMENTS_CATEGORY => 'id' ])
            ->join([ ContentDB::ACHIEVEMENTS_CATEGORY => 'achievement_kind' ], [ ContentDB::ACHIEVEMENTS_KIND => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::ACHIEVEMENTS))
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

    }

    //
    // Manual gamedata modification
    //
    public function manual()
    {
        $language = $this->getModule('language');
        $dbs = $this->getModule('database');

        // If not extended and not tooltip
        if (!$this->isFlagged('search') && !$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // set its type
            $this->data['type_name'] = $language->custom($this->types[$this->data['type']]);

            // Add post achievements
            $this->data['post_achievements'] = null;
            if ($this->hasColumn('connect_post')) {
                foreach($this->join(ContentDB::ACHIEVEMENTS, ContentDB::TO_ACHIEVEMENT_POST, 'post_achievement', 'achievement', ['id']) as $res) {
                    $this->data['post_achievements'][] = $this->addAchievement($res['id']);
                }
            }

            // Add pre achievements
            $this->data['pre_achievements'] = null;
            if ($this->hasColumn('connect_pre')) {
                foreach($this->join(ContentDB::ACHIEVEMENTS, ContentDB::TO_ACHIEVEMENT_PRE, 'pre_achievement', 'achievement', ['id']) as $res) {
                    $this->data['pre_achievements'][] = $this->addAchievement($res['id']);
                }
            }

            // Add quests
            $this->data['pre_quests'] = null;
            if ($this->hasColumn('connect_quest')) {
                foreach($this->join(ContentDB::QUEST, ContentDB::TO_ACHIEVEMENT_QUESTS, 'quest', 'achievement', ['id']) as $res) {
                    $this->data['pre_quests'][] = $this->addQuest($res['id']);
                }
            }

            // Add title
            $this->data['title'] = $this->hasColumn('title') ? $this->addTitle($this->data['title']) : null;

            // Add item
            $this->data['item'] = $this->hasColumn('item') ? $this->addItem($this->data['item']) : null;

            // add different types of goals and requirements
            switch ($this->data['type'])
            {
                case 1: $this->addActivityRequirement(); break;
                case 3: $this->addClassJobRequirements(); break;
                case 6: case 9: $this->addQuestRequirements(); break;
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
    // Activity requirement, eg kill 10,000 enemies
    //
    public function addActivityRequirement()
    {
        $this->data['other_requirements'] = [
            $this->data['requirement_2'],
        ];
    }

    //
    // Add class job requirements
    //
    public function addClassJobRequirements()
    {
        $this->data['other_requirements'] = [
            'classjob' => $this->addClassJob($this->data['requirement_1']),
            'level' => $this->data['requirement_2'],
        ];
    }

    //
    // Add quest requirements
    //
    public function addQuestRequirements()
    {
        $this->data['other_requirements'] = [];

        // if require == 66656
        foreach([1,2,3,4,5,6,7,8,9] as $num) {
            $idx = 'requirement_'. $num;

            // add quest if ID is in range of quest (some are not)
            if (in_array(substr($this->data[$idx], 0, 2), [65, 66, 67])) {
                $qid = $this->data[$idx];

                // if not in the pre quest requirements
                if (!isset($this->data['pre_quests'][$qid])) {
                    $this->data['requirements'][] = $this->addQuest($qid);
                }
            }
        }
    }
}
