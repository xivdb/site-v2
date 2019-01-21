<?php

namespace XIVDB\Apps\Content;

class Minion extends Content
{
    const TYPE = 'minion';

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
        'summon_ja',
        'summon_en',
        'summon_de',
        'summon_fr',
        'summon_cns',
        'info1_ja',
        'info1_en',
        'info1_de',
        'info1_fr',
        'info1_cns',
        'info2_ja',
        'info2_en',
        'info2_de',
        'info2_fr',
        'info2_cns',
        'action_ja',
        'action_en',
        'action_fr',
        'action_de',
        'action_cns',
        'help_ja',
        'help_en',
        'help_fr',
        'help_de',
        'help_cns',
        'icon',
        'behavior',
        'cost',
        'hp',
        'skill_cost',
        'minion_race',
        'attack',
        'defense',
        'speed',
        'has_area_attack',
        'strength_gate',
        'strength_eye',
        'strength_shield',
        'strength_arcana',
        'minion_skill_type',
        'patch',
        'lodestone_id',
        'lodestone_type',

        // too many for a "basic" content include
        'name_plural_{lang} as name_plural',
        'summon_{lang} as summon',
        'info1_{lang} as info1',
        'info2_{lang} as info2',
        'action_{lang} as action',
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
        'icon',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'minion_race' => 'Race',
        'cost' => 'Cost',
        'skill_cost' => 'Skill Cost',
        'hp' => 'Health',
        'attack' => 'Attack',
        'defense' => 'Defense',
        'speed' => 'Speed',
        'has_area_attack' => 'Has AOE',
        'strength_gate' => 'Strong against Gate',
        'strength_eye' => 'Strong against Search Eye',
        'strength_shield' => 'Strong against Shield',
        'strength_arcana' => 'Strong against Arcana Stones',
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
            ->from(ContentDB::MINIONS)
            ->addColumns([ ContentDB::MINIONS => array_merge(
                $this->isFlagged('extended') ? Minion::$basic : Minion::$main,
                Minion::$language)
            ])
            ->addColumns([ ContentDB::MINIONS_RACE => [ 'name_{lang} as race' ] ])
            ->join([ ContentDB::MINIONS => 'minion_race' ], [ ContentDB::MINIONS_RACE => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::MINIONS))
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
            // Add behaviour
            $dbs->QueryBuilder->select(['name_{lang} as name'])->from('xiv_companions_move')->where('id = :id')->bind('id', $this->data['behavior']);
            $this->data['behavior'] = $dbs->get()->one()['name'];

            // Add race
            $dbs->QueryBuilder->select(['name_{lang} as name'])->from('xiv_companions_race')->where('id = :id')->bind('id', $this->data['minion_race']);
            $this->data['minion_race'] = $dbs->get()->one()['name'];


            // Add skill type
            $dbs->QueryBuilder->select(['name_{lang} as name'])->from('xiv_companions_skill_type')->where('id = :id')->bind('id', $this->data['minion_skill_type']);
            $this->data['minion_skill_type'] = $dbs->get()->one()['name'];
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

        if (isset($this->data['speed'])) {
            $this->data['speed_html'] = '&#9733;';

            if ($this->data['speed'] > 0) {
                for ($i=0; $i < $this->data['speed']; $i++) {
                    $this->data['speed_html'] .= '&#9733;';
                }
            }
        }
    }
}
