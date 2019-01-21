<?php

namespace XIVDB\Apps\Content;

class Item extends Content
{
    use ItemTrait;

    const TYPE = 'item';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'noun_ja',
        'noun_en',
        'noun_fr',
        'noun_de',
        'noun_cns',
        'help_ja',
        'help_en',
        'help_fr',
        'help_de',
        'help_cns',
        'plural_ja',
        'plural_en',
        'plural_fr',
        'plural_de',
        'plural_cns',
        'classjob_category',
        'stack_size',
        'action',
        'level_equip',
        'level_item',
        'rarity',
        'item_special_bonus',
        'item_series',
        'slot_equip',
        'price_low',
        'price_mid',
        'price_high',
        'price_sell',
        'price_sell_hq',
        'icon',
        'icon_lodestone',
        'icon_lodestone_hq',
        'salvage',
        'materia_slot_count',
        'materialize_type',
        'starts_with_vowel',
        'pvp_rank',
        'stain',
        'model_main',
        'model_sub',
        'classjob_use',
        'cooldown_seconds',
        'equip_slot_category',
        'gc_turn_in',
        'grand_company',
        'base_param_modifier',
        'aetherial_reduce',
        'desynthesize',
        'can_be_hq',
        'item_ui_kind',
        'item_ui_category',
        'item_search_category',
        'item_action',
        'item_duration',
        'item_glamour',
        'item_repair',
        'reducible_classjob',
        'reducible_level',
        'classjob_repair',
        'equippable_by_race_hyur',
        'equippable_by_race_elezen',
        'equippable_by_race_lalafell',
        'equippable_by_race_miqote',
        'equippable_by_race_roegadyn',
        'equippable_by_race_aura',
        'equippable_by_gender_m',
        'equippable_by_gender_f',
        'lodestone_type',
        'lodestone_id',
        'sort_key',
        'is_pvp',
        'is_unique',
        'is_untradable',
        'is_reducible',
        'is_legacy',
        'is_dated',
        'is_crest_worthy',
        'is_desynthesizable',
        'is_projectable',
        'is_dyeable',
        'is_convertible',
        'is_indisposable',
        'is_collectable',
        'parsed_lodestone',
        'parsed_lodestone_time',
        'patch',
        'updated',
        'connect_instance',
        'connect_instance_chest',
        'connect_instance_reward',
        'connect_quest_reward',
        'connect_enemy_drop',
        'connect_recipe',
        'connect_craftable',
        'connect_gathering',
        'connect_achievement',
        'connect_shop',
        'connect_leve',
        'connect_specialshop_receive_1',
        'connect_specialshop_receive_2',
        'connect_specialshop_cost_1',
        'connect_specialshop_cost_2',
        'connect_specialshop_cost_3',

        'noun_{lang} as noun',
        'plural_{lang} as plural',
    ];

    // Basic columns (extended)
    public static $basic =
    [
        'id',
        'level_equip',
        'level_item',
        'icon',
        'icon_lodestone',
        'lodestone_type',
        'lodestone_id',
        'classjob_category',
        'rarity',
        'patch',
        'price_mid',
        'slot_equip',
        'item_ui_category',
        'stack_size',
        'updated',

        'connect_instance',
        'connect_instance_chest',
        'connect_instance_reward',
        'connect_quest_reward',
        'connect_enemy_drop',
        'connect_recipe',
        'connect_craftable',
        'connect_gathering',
        'connect_achievement',
        'connect_shop',
        'connect_leve',
        'connect_specialshop_receive_1',
        'connect_specialshop_receive_2',
        'connect_specialshop_cost_1',
        'connect_specialshop_cost_2',
        'connect_specialshop_cost_3',
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
        'level_equip',
        'level_item',
        'rarity',
        'icon',
        'icon_lodestone',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'slot_equip' => 'Slot',
        'level_equip' => 'Equipment Level',
        'level_item' => 'Item Level',
        'rarity' => 'Rarity',
        'stack_size' => 'Stack Size',
        'price_sell' => 'Price Sell',
        'price_sell_hq' => 'Price Sell HQ',
        'salvage' => 'Salvage',
        'materia_slot_count' => 'Materia Slots',
        'pvp_rank' => 'PvP Rank',
        'cooldown_seconds' => 'Cooldown',
        'aetherial_reduce' => 'Aetherial Reduce',
        'can_be_hq' => 'Can HQ',
        'reducible_level' => 'Reducible Level',
        'is_pvp' => 'Is PvP',
        'is_untradable' => 'Is Untradable',
        'is_reducible' => 'Is Reducible',
        'is_legacy' => 'Is Legacy',
        'is_dated' => 'Is Dated',
        'is_crest_worthy' => 'Is Crest Worthy',
        'is_desynthesizable' => 'Is Desynthesizable',
        'is_projectable' => 'Is Projectable',
        'is_dyeable' => 'Is Dyeable',
        'is_convertible' => 'Is Convertible',
        'is_indisposable' => 'Is Indisposable',
        'is_collectable' => 'Is Collectable',
        'updated' => 'Last Updated',
        'parsed_lodestone' => 'Verified on Lodestone',
        'patch' => 'Patch',
        'attributes' => 'Attributes',
    ];

    public static $unset = [
        'achievements',
        'attributes_base',
        'attributes_params',
        'classjob_category',
        'classjob_repair',
        'classjobs',
        'connected',
        'craftable',
        'enemies',
        'gathering',
        'icon_hq',
        'icon_lodestone',
        'instances',
        'item_glamour',
        'item_repair',
        'leves',
        'quests',
        'recipes',
        'reducible_classjob',
        'reward',
        'shops',
        'special_shops_currency',
        'special_shops_obtain',
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
            ->from(ContentDB::ITEMS)
            ->addColumns([ ContentDB::ITEMS => array_merge(
                $this->isFlagged('extended') ? Item::$basic : Item::$main,
                Item::$language)
            ])
            ->addColumns([ ContentDB::TO_SERIES => [ 'name_{lang} as series_name' ] ])
            ->addColumns([ ContentDB::TO_BONUS => [ 'name_{lang} as bonus_name' ] ])
            ->addColumns([ ContentDB::TO_CATEGORY => [ 'name_{lang} as category_name' ] ])
            ->addColumns([ ContentDB::TO_KIND => [ 'name_{lang} as kind_name' ] ])
            ->addColumns([ ContentDB::TO_SLOT => [ 'name_{lang} as slot_name' ] ])
            ->join([ ContentDB::ITEMS => 'item_series' ], [ ContentDB::TO_SERIES => 'id' ])
            ->join([ ContentDB::ITEMS => 'item_special_bonus' ], [ ContentDB::TO_BONUS => 'id' ])
            ->join([ ContentDB::ITEMS => 'item_ui_category' ], [ ContentDB::TO_CATEGORY => 'id' ])
            ->join([ ContentDB::TO_CATEGORY => 'item_ui_kind' ], [ ContentDB::TO_KIND => 'id' ])
            ->join([ ContentDB::ITEMS => 'slot_equip' ], [ ContentDB::TO_SLOT => 'id' ])
            ->limit(0,1);

        $qq = is_numeric($this->id)
            ? $sql->where(sprintf('%s.id = :id', ContentDB::ITEMS))->bind('id', $this->id)
            : $sql->where(sprintf('%s.lodestone_id = :lodestone_id', ContentDB::ITEMS))->bind('lodestone_id', $this->id);

        // return
        $this->data = $dbs->get()->one();
        return $this;
    }

    //
    // Provide tooltip data
    //
    public function tooltip()
    {
        return [
            'name' => $this->data['name'],
            'color' => $this->data['color'],
            'icon' => $this->data['icon'],
        ];
    }

    //
    // Attach linked content
    //
    public function extended()
    {
        $dbs = $this->getModule('database');

        // Add on class job category
        $this->data['classjob_category'] = $this->hasColumn('classjob_category')
            ? $this->addClassJobCategory($this->data['classjob_category']) : null;

        // Add attributes
        $this->data['attributes_base'] = $this->addBaseAttributes($this->id);
        $this->data['attributes_params'] = $this->addParamAttributes($this->id);
        $this->data['attributes_params_special'] = $this->addSpecialParamAttributes($this->id);

        $canConnection = $this->data['id'] > $this->minConnectionId ? true : false;
        $this->data['connected'] = $canConnection;

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            // Add class jobs
            $this->data['classjobs'] = $this->join(ContentDB::CLASSJOB, ContentDB::ITEMS_TO_CLASSJOB, 'classjob', 'item', [
                'id', 'name_{lang} as name', 'abbr_{lang} as abbr', 'is_job', 'icon', 'patch'
            ]);

            // Add repair item
            $this->data['item_repair'] = $this->hasColumn('item_repair')
                ? $this->addItem($this->data['item_repair']) : null;

            // Add glamour item
            $this->data['item_glamour'] = $this->hasColumn('item_glamour')
                ? $this->addItem($this->data['item_glamour']) : null;

            // Add class job repair
            $this->data['classjob_repair'] = $this->hasColumn('classjob_repair')
                ? $this->addClassJob($this->data['classjob_repair']) : null;

            // Add reducible classjob
            $this->data['reducible_classjob'] = $this->hasColumn('reducible_classjob')
                ? $this->addClassJob($this->data['reducible_classjob']) : null;
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {
            // Add instances
            $this->data['instances'] = null;
            if ($canConnection && $this->hasColumn('connect_instance')) {
                foreach($this->join(ContentDB::INSTANCES, ContentDB::TO_INSTANCE, 'instance', 'item', ['id']) as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['instances'][$res['id']] = $this->addInstance($res['id']);
                }
            }

            // Add leves
            $this->data['leves'] = null;
            if ($canConnection && $this->hasColumn('connect_leve')) {
                foreach($this->join(ContentDB::LEVES, ContentDB::TO_LEVES, 'leve', 'item', ['id']) as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['leves'][$res['id']] = $this->addLeve($res['id']);
                }
            }

            // Add instance chests
            if ($canConnection && $this->hasColumn('connect_instance_chest')) {
                $dbs->QueryBuilder
                    ->select('instance, boss')
                    ->from(ContentDB::TO_INSTANCE_CHEST)
                    ->where('item = :id')->bind('id', $this->id);

                // add chests if any found
                foreach($dbs->get()->all() as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['instances'][$res['instance']] = $this->addInstance($res['instance']);
                }
            }

            // Add achievement
            $this->data['achievements'] = null;
            if ($canConnection && $this->hasColumn('connect_achievement')) {
                $dbs->QueryBuilder
                    ->select('id')
                    ->from(ContentDB::ACHIEVEMENTS)
                    ->where('item = :id')->bind('id', $this->id);

                foreach($dbs->get()->all() as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['achievements'][] = $this->addAchievement($res['id']);
                }
            }

            // Add questz
            $this->data['quests'] = null;
            if ($canConnection && $this->hasColumn('connect_quest_reward')) {
                $list = $this->join(ContentDB::QUEST, ContentDB::TO_QUESTS, 'quest', 'item', ['id']);
                if (count($list) > $this->maxConnections) {
                    $this->data['quests_splice'] = count($list);
                }

                foreach($list as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['quests'][] = $this->addQuest($res['id']);
                }

                $this->sksort($this->data['quests'], 'genre', 'desc');
            }

            // Add shops
            $this->data['shops'] = null;
            if ($canConnection && $this->hasColumn('connect_shop')) {
                foreach($this->join(ContentDB::SHOP, ContentDB::TO_SHOP, 'shop', 'item', ['id']) as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['shops'][] = $this->addShop($res['id']);
                }
            }

            // Add "crafts into"
            $this->data['recipes'] = null;
            if ($canConnection && $this->hasColumn('connect_recipe')) {
                $list = $this->join(ContentDB::RECIPE, ContentDB::TO_RECIPE, 'recipe', 'item', ['id']);
                if (count($list) > $this->maxConnections) {
                    $this->data['recipes_splice'] = count($list);
                }

                foreach($list as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['recipes'][] = $this->addRecipe($res['id']);
                }
            }

            // Add enemy
            $this->data['enemies'] = null;
            if ($canConnection && $this->hasColumn('connect_enemy_drop')) {
                foreach($this->join(ContentDB::ENEMY, ContentDB::TO_ENEMY, 'enemy', 'item', ['id']) as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['enemies'][] = $this->addEnemy($res['id']);
                }
            }

            // Add gathering
            $this->data['gathering'] = null;
            if ($canConnection && $this->hasColumn('connect_gathering')) {
                $dbs->QueryBuilder->select('id')->from(ContentDB::GATHERING)->where('item = :id')->bind('id', $this->id);

                foreach($dbs->get()->all() as $i => $res) {
                    if ($i == $this->maxConnections) {
                        break;
                    }

                    $this->data['gathering'][] = $this->addGathering($res['id']);
                }
            }

            // Handle special shops (5 queries)
            $this->handleSpecialShops();

            //
            // Split help onto multiple lines
            //
            if (isset($this->data['help'])) {
                $this->data['help'] = str_ireplace('. ', '.<br><br>', $this->data['help']);
            }
        }

        // Add "crafted from"
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip') || $this->isFlagged('cart'))
        {
            $this->data['craftable'] = null;
            if ($canConnection && $this->hasColumn('connect_craftable')) {
                $dbs->QueryBuilder->select('id')->from(ContentDB::RECIPE)->where('item = :id')->bind('id', $this->id);

                foreach($dbs->get()->all() as $res) {
                    $this->data['craftable'][] = $this->addRecipe($res['id'], $this->quantity, true, false);
                }
            }
        }

        // sorting
        if (isset($this->data['instances']) && $this->data['instances']) {
            $this->sksort($this->data['instances'], 'level');
        }

        if (isset($this->data['recipes']) && $this->data['recipes']) {
            $this->sksort($this->data['recipes'], 'level_view');
            $this->sksort($this->data['recipes'], 'level_diff');
        }

        // sort back into arrays
        $this->cleanArrays();
    }

    //
    // Manual gamedata modification
    //
    public function manual()
    {
        $this
            ->setDescriptionColours('help')    // set colors in help text
            ->setRepairLevel()      // set repair level
            ->setRepairPrice()      // set the repair price
            ->setClassJobIcons();   // set class job icons

        // if materia, repair is catalyst
        if (isset($this->data['item_repair']) && isset($this->data['item_ui_category']) && $this->data['item_ui_category'] == 58) {
            $this->data['item_catalyst'] = $this->data['item_repair'];
            $this->data['item_repair'] = null;

            if (isset($this->data['item_repair']) && !isset($this->data['item_repair']['name'])) {
                $this->data['item_repair'] = null;
            }

            if (isset($this->data['classjob_repair']) && !isset($this->data['classjob_repair']['name'])) {
                $this->data['classjob_repair'] = null;
            }
        }
    }

    private function cleanArrays()
    {

        if (isset($this->data['instances'])) {
            $this->data['instances'] = array_values($this->data['instances']);
        }

        if (isset($this->data['leves'])) {
            $this->data['leves'] = array_values($this->data['leves']);
        }

        if (isset($this->data['achievements'])) {
            $this->data['achievements'] = array_values($this->data['achievements']);
        }

        if (isset($this->data['quests'])) {
            $this->data['quests'] = array_values($this->data['quests']);
        }

        if (isset($this->data['shops'])) {
            $this->data['shops'] = array_values($this->data['shops']);
        }

        if (isset($this->data['recipes'])) {
            $this->data['recipes'] = array_values($this->data['recipes']);
        }

        if (isset($this->data['enemies'])) {
            $this->data['enemies'] = array_values($this->data['enemies']);
        }

        if (isset($this->data['gathering'])) {
            $this->data['gathering'] = array_values($this->data['gathering']);
        }

        if (isset($this->data['craftable'])) {
            $this->data['craftable'] = array_values($this->data['craftable']);
        }
    }
}
