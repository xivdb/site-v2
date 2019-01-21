<?php

namespace XIVDB\Apps\Content;

class Action extends Content
{
    const TYPE = 'action';

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
        'json_ja',
        'json_en',
        'json_fr',
        'json_de',
        'json_cns',
        'icon',
        'level',
        'classjob_category',
        'classjob',
        'spell_group',
        'can_target_self',
        'can_target_party',
        'can_target_friendly',
        'can_target_hostile',
        'can_target_dead',
        'status_required',
        'status_gain_self',
        'cost',
        'cost_hp',
        'cost_mp',
        'cost_tp',
        'cost_cp',
        'cast_range',
        'cast_time',
        'recast_time',
        'is_in_game',
        'is_trait',
        'is_pvp',
        'is_target_area',
        'action_category',
        'action_combo',
        'action_proc_status',
        'action_timeline_hit',
        'action_timeline_use',
        'action_data',
        'effect_range',
        'type',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'icon',
        'level',
        'lodestone_id',
        'lodestone_type',
        'type',
        'patch',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
        'help_{lang} as help',
        'json_{lang} as json',
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
        'level',
        'classjob',
        'type',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'level' => 'level',
        'cost' => 'Cost',
        'cast_range' => 'Cast Range',
        'cast_time' => 'Cast Time',
        'recast_time' => 'Recast Time',
        'classjob' => 'Class Job',
        'type' => 'Type',
        'can_target_self' => 'Can Target Self',
        'can_target_party' => 'Can Target Party',
        'can_target_friendly' => 'Can Target Friendly',
        'can_target_hostile' => 'Can Target Hostile',
        'is_target_area' => 'Is AOE',
        'is_pvp' => 'Is PVP',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'classjob',
        'classjob_category',
        'icon_hq',
        'type',
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
            ->from(ContentDB::ACTIONS)
            ->addColumns([ ContentDB::ACTIONS => array_merge(
                $this->isFlagged('extended') ? Action::$basic : Action::$main,
                Action::$language)
            ])
            ->addColumns([ ContentDB::CLASSJOB => [ 'name_{lang} as class_name' ] ])
            ->join([ ContentDB::ACTIONS => 'classjob' ], [ ContentDB::CLASSJOB => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::ACTIONS))
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

        // Add classjob
        $this->data['classjob'] = $this->hasColumn('classjob') ? $this->addClassJob($this->data['classjob']) : null;
        $this->data['classjob_category'] = $this->hasColumn('classjob_category') ? $this->addClassJobCategory($this->data['classjob_category']) : null;

        // Extras
        if (!$this->isFlagged('search'))
        {
            $this->setHelpTextColors();
            $this->setUpgradeLevels();
        }
    }

    //
    // Set general game data
    //
    public function setGameData()
    {
        // set type
        $language = $this->getModule('language');
        $this->data['type_name'] = null;
        switch ($this->data['type'])
        {
            case 1: $this->data['type_name'] = $language->custom(851); break;
            case 2: $this->data['type_name'] = $language->custom(453); break;
            case 3: $this->data['type_name'] = $language->custom(234); break;
        }

        if (!$this->isFlagged('search'))
        {
            // decode json
            $this->data['json'] = json_decode($this->data['json'], true);
        }


        // fix class job if its -1
        if (!isset($this->data['classjob']) || $this->data['classjob'] == '-1') {
            $this->data['classjob'] = null;
        }


    }

    //
    // Handle action help data
    //
    public function setHelpTextColors()
    {
        // add colors to help text
        if (!isset($this->data['help'])) {
            return;
        }

        $this->data['help'] = $this->colors($this->data['help']);

        // change some of the text
        $findAndReplace = [
            'Efficiency:' => "<em class=\"lime\">Efficiency:</em>",
            'Potency:' => "<em class=\"lime\">Potency:</em>",
            'Success Rate:' => "<em class=\"lime\">Success Rate:</em>",
            'Additional Effect:' => "\n<em class=\"yellow\">Additional Effect:</em>",
            'Duration:' => "<em class=\"lime\">Duration:</em>",
            'Cure Potency:' => "<em class=\"sky\">Cure Potency:</em>",
            'Cannot Be Used In PvP Areas.' => "\n\n<em class=\"orange\">Cannot Be Used In PvP Areas.</em>",
            'Shares a recast timer with' => "\n\nShares a recast timer with",
        ];
    
        $this->data['help'] = nl2br($this->data['help']);
        $this->data['help_html'] = str_replace(array_keys($findAndReplace), $findAndReplace, $this->data['help']);
    }

    //
    // Upgrade levels
    //
    public function setUpgradeLevels()
    {
        $this->data['upgrades'] = [];
        if ($this->data['json']) {
            $json = json_decode($this->data['json'], true);
            $this->setUpgradeLevelsRecurrsion($json);
        }

        $this->data['upgrades'] = array_reverse($this->data['upgrades']);
    }

    //
    // Upgrade level recurrsion
    //
    public function setUpgradeLevelsRecurrsion($json)
    {
        if (!$json) {
            return;
        }
        
        foreach($json as $i => $data)
        {
            if (is_array($data))
            {
                foreach($data as $type => $moredata)
                {
                    if ($type == 'condition' && $moredata['left_operand'] == 'level')
                    {
                        $this->data['upgrades'][] = 'Lv '. $moredata['right_operand'];
                    }
                    else if (is_Array($moredata))
                    {
                        $this->setUpgradeLevelsRecurrsion($moredata);
                    }
                }
            }
        }
    }
}
