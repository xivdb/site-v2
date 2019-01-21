<?php

namespace XIVDB\Apps\Content;

class Instance extends Content
{
    const TYPE = 'instance';

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
        'instance_content_type',
        'content_type',
        'sort',
        'banner',
        'icon',
        'level',
        'level_sync',
        'time_limit',
        'halfway',
        'random_content_type',
        'alliance',
        'party_finder_condition',
        'players_per_party',
        'tanks_per_party',
        'healers_per_party',
        'melees_per_party',
        'ranged_per_party',
        'differentiates_dps',
        'party_count',
        'free_role',
        'item_level',
        'item_level_sync',
        'colosseum',
        'placename',
        'force_party_setup',
        'lodestone_type',
        'lodestone_id',
        'is_echo_default',
        'is_echo_annihilation',
        'content_roulette',
        'week_restriction',
        'party_condition',
        'is_in_duty_finder',
        'territory_id',
        'boss',
        'instance_content_text_data_boss_start',
        'instance_content_text_data_boss_end',
        'instance_content_text_data_objective_start',
        'instance_content_text_data_objective_end',
        'sort_key',
        'allow_alliance_registration',
        'patch',
        'currency_a_bonus',
        'currency_b_bonus',
        'currency_c_bonus',
        'currency_a',
        'currency_b',
        'currency_c',
        'currency_d',
        'connect_chests',
        'connect_bosses',
        'connect_currency',
        'connect_enemy',
        'connect_items',
        'connect_rewards'
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'banner',
        'icon',
        'level',
        'level_sync',
        'item_level',
        'item_level_sync',
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
        'level',
        'level_sync',
        'item_level',
        'item_level_sync',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'level' => 'Level',
        'level_sync' => 'Sync Level',
        'time_limit' => 'Time Limit',
        'players_per_party' => 'Players per Party',
        'tanks_per_party' => 'Tanks per Party',
        'healers_per_party' => 'Healers per Party',
        'melees_per_party' => 'Melees per Party',
        'ranged_per_party' => 'Ranged per Party',
        'party_count' => 'Party Count',
        'free_role' => 'Can free role',
        'item_level' => 'Item Level Required',
        'item_level_sync' => 'Item Level Sync',
        'is_echo_default' => 'Echo by Default',
        'is_echo_annihilation' => 'Echo upon Death',
        'week_restriction' => 'Weekly Restricted',
        'is_in_duty_finder' => 'Is in duty-finder',
        'allow_alliance_registration' => 'Allow Alliance Registration',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'content_icon_mini',
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
            ->from(ContentDB::INSTANCES)
            ->addColumns([ ContentDB::INSTANCES => array_merge(
                $this->isFlagged('extended') ? Instance::$basic : Instance::$main,
                Instance::$language)
            ])
            ->addColumns([ ContentDB::INSTANCES_ROULETTE => [ 'name_{lang} as roulette_name' ] ])
            ->addColumns([ ContentDB::CONTENT_TYPE => [ 'name_{lang} as content_name', 'icon as content_icon', 'icon_mini as content_icon_mini' ] ])
            ->join([ ContentDB::INSTANCES => 'content_roulette' ], [ ContentDB::INSTANCES_ROULETTE => 'id' ])
            ->join([ ContentDB::INSTANCES => 'instance_content_type' ], [ ContentDB::INSTANCES_TYPE => 'id' ])
            ->join([ ContentDB::INSTANCES_TYPE => 'content_type' ], [ ContentDB::CONTENT_TYPE => 'id' ])
            ->where(sprintf('%s.id = :id', ContentDB::INSTANCES))
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
            //
            // Add placename
            //
            $this->data['placename'] = $this->hasColumn('placename')
                ? $this->addPlacename($this->data['placename'])
                : null;

            //
            // Set content from map markers
            //
            $this->data['enemies'] = [];
            $this->data['npcs'] = [];
            $this->data['gathering'] = [];
            if ($this->data['placename']) {
                $placenameId = $this->data['placename']['id'];
                $this->setContentFromMapMarkers($placenameId);
            }

            //
            // Add tomestone currency items
            //
            $tomestones = $this->addTomestonesItems();
            $this->data['currency_a_item'] = null; // $tomestones['a'] ?? null;
            $this->data['currency_b_item'] = null; // $tomestones['b'] ?? null;
            $this->data['currency_c_item'] = null; // $tomestones['c'] ?? null;

            //
            // Add quest rewards
            //
            $this->data['quest_rewards'] = null;
            if ($this->data['connect_rewards']) {
                $dbs->QueryBuilder
                    ->select('item, quest')
                    ->from(ContentDB::TO_INSTANCE_REWARD)
                    ->where('instance = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $i => $res) {
                    $this->data['quest_rewards'][] = [
                        'item' => $this->addItem($res['item']),
                        'quest' => $this->addWebExQuest($res['quest']),
                    ];
                }
            }

            //
            // Add bosses
            //
            $this->data['bosses'] = null;
            if ($this->data['connect_bosses']) {
                $dbs->QueryBuilder
                    ->select('instance, npc_enemy')
                    ->from(ContentDB::TO_INSTANCE_ENEMY)
                    ->where('instance = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $i => $res) {
                    $this->data['bosses'][] = $this->addEnemy($res['npc_enemy']);
                }
            }

            //
            // Add items
            //
            $this->data['items'] = null;
            $this->data['items_total'] = 0;
            if ($this->data['connect_items']) {
                $dbs->QueryBuilder
                    ->select('item')
                    ->from(ContentDB::TO_INSTANCE)
                    ->where('instance = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    $this->data['items'][$res['item']] = $this->addItem($res['item']);
                }
            }

            //
            // Add items from chests
            //
            if ($this->data['connect_chests']) {
                $dbs->QueryBuilder
                    ->select('*')
                    ->from(ContentDB::TO_INSTANCE_CHEST)
                    ->where('instance = :id')
                    ->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    // if already added to "items" then skip
                    if (isset($this->data['items'][$res['item']])) {
                        continue;
                    }

                    $this->data['items'][$res['item']] = $this->addItem($res['item']);
                }
            }

            //
            // sort items into "kind_name"
            //
            if ($this->data['items'])
            {
                $this->data['items_total'] = count($this->data['items']);

                $temp = [];
                foreach($this->data['items'] as $item) {
                    if ($item['id'] == 1) {
                        $this->data['items_total']--;
                        continue;
                    }

                    $kind = $item['kind_name'];
                    $category = $item['category_name'];
                    $temp[$kind][$category][] = $item;
                }

                foreach($temp as $kind => $categories) {
                    foreach($categories as $category => $item) {
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
    }

    //
    // Set game data
    //
    public function setGameData()
    {
        if (isset($this->data['banner'])) {
            $this->data['banner'] = SECURE . $this->iconize($this->data['banner']);
        }

        if (isset($this->data['content_icon']) && $this->data['content_icon']) {
            $this->data['icon'] = SECURE . $this->iconize($this->data['content_icon']);
            unset($this->data['content_icon']);
        } else {
            $this->data['icon'] = SECURE . '/img/ui/noicon.png';
        }

        if (isset($this->data['content_icon_mini']) && $this->data['content_icon_mini']) {
            $this->data['icon_mini'] = SECURE . $this->iconize($this->data['content_icon_mini']);
            unset($this->data['content_icon_mini']);
        }

        if (isset($this->data['roulette_name']) && stripos($this->data['roulette_name'], ':') !== false) {
            $this->data['roulette_name_short'] = trim(explode(':', $this->data['roulette_name'])[1]);
        }

        // total number of dps per party
        if (isset($this->data['melees_per_party'])) {
            $this->data['dps_per_party'] = ($this->data['melees_per_party'] + $this->data['ranged_per_party']);
        }
    }
}
