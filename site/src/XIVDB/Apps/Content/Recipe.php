<?php

namespace XIVDB\Apps\Content;

class Recipe extends Content
{
    const TYPE = 'recipe';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'can_quick_synth',
        'can_hq',
        'craft_quantity',
        'craft_type',
        'classjob',
        'level',
        'level_view',
        'level_diff',
        'item',
        'recipe_notebook',
        'recipe_element',
        'is_secondary',
        'is_specialization_required',
        'difficulty_factor',
        'durability_factor',
        'quality_factor',
        'quick_synth_craftsmanship',
        'quick_synth_control',
        'unlock_key',
        'required_craftsmanship',
        'required_control',
        'status_required',
        'item_required',
        'lodestone_type',
        'lodestone_id',
        'material_point',
        'quality',
        'durability',
        'difficulty',
        'work_max',
        'number',
        'patch'
    ];

    // Basic column
    public static $basic =
    [
        'id',
        'craft_quantity',
        'level',
        'level_view',
        'level_diff',
        'classjob',
        'item',
        'lodestone_type',
        'lodestone_id',
        'patch'
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
    ];

    // Search columns
    public static $search =
    [
        'id',
        'name_{lang} as name',
        'classjob',
        'level',
        'level_view',
        'level_diff',
    ];

    // (join) Item columns
    public static $item =
    [
        'name_{lang} as item_name',
        'icon as item_icon',
        'icon_lodestone as item_icon_lodestone',
        'rarity as item_rarity'
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'level' => 'Recipe Level',
        'level_diff' => 'Stars',
        'level_view' => 'Craft Level',
        'durability_factor' => 'Durability',
        'quick_synth_craftsmanship' => 'Quick Synth Craftsmanship',
        'quick_synth_control' => 'Quick Synth Control',
        'required_craftsmanship' => 'Required Craftsmanship',
        'required_control' => 'Required Control',
        'status_required' => 'Status Required',
        'item_required' => 'Item Required',
        'material_point' => 'Material Point',
        'quality' => 'Quality',
        'durability' => 'Durability',
        'difficulty' => 'Difficulty',
        'work_max' => 'Max Work',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'classjob',
        'item_rarity',
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
            ->from(ContentDB::RECIPE)
            ->addColumns([ ContentDB::RECIPE => array_merge(
                $this->isFlagged('extended') && !$this->getAll ? Recipe::$basic : Recipe::$main,
                Recipe::$language)
            ])

            ->addColumns([ ContentDB::ITEMS => Recipe::$item ])
            ->addColumns([ ContentDB::CLASSJOB => [ 'name_{lang} as class_name' ] ])
            ->addColumns([ ContentDB::RECIPE_ELEMENT => [ 'name_{lang} as element_name' ] ])

            ->join([ ContentDB::RECIPE => 'recipe_element' ], [ ContentDB::RECIPE_ELEMENT => 'id' ])
            ->join([ ContentDB::RECIPE => 'item' ], [ ContentDB::ITEMS => 'id' ])
            ->join([ ContentDB::RECIPE => 'craft_type' ], [ ContentDB::RECIPE_TYPE => 'id' ])
            ->join([ ContentDB::RECIPE_TYPE => 'classjob' ], [ ContentDB::CLASSJOB => 'id' ])

            ->where(sprintf('%s.id = :id', ContentDB::RECIPE))
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

        // add item
        if (!$this->isFlagged('search'))
        {
            $this->data['item'] = $this->addItem($this->data['item']);

            // build tree
            $this->data['tree'] = $this->getTree();

            // add classjob
            $this->data['classjob'] = $this->hasColumn('classjob') ? $this->addClassJob($this->data['classjob']) : null;
        }

        // Stuff to include when not extended
        if (!$this->isFlagged('extended'))
        {
            // masterbook
            $this->data['masterbook'] = null;
            if (isset($this->data['unlock_key']) && intval($this->data['unlock_key']) > 0) {
                $masterBookId = $this->data['unlock_key'];
                $dbs = $this->getModule('database');
                $dbs->QueryBuilder->select('item_id', false)->from(ContentDB::RECIPE_MASTERBOOKS)
                    ->where('id = :id')->bind('id', $masterBookId);

                if ($masterBookEntry = $dbs->get()->one()) {
                    $itemId = $masterBookEntry['item_id'];
                    $this->data['masterbook'] = $this->addItem($itemId);
                }
            }
        }
    }

    //
    // Crafting tree
    //
    public function getTree()
    {
        $tree = [];

        // get tree items
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->select('item, quantity')->from(ContentDB::TO_RECIPE)->where('recipe = :id')->bind('id', $this->id);

        // handle tree items
        foreach($dbs->get()->all() as $i => $treeitem)
        {
            // get the item
            $item = $this->addItem($treeitem['item']);

            // multiply required materials by the number of items we want from this recipe
            $this->quantity = $this->quantity > 0 ? $this->quantity : 1;
            $this->data['craft_quantity'] = $this->data['craft_quantity'] > 0 ? $this->data['craft_quantity'] : 1;
            $item['quantity'] = $treeitem['quantity'] * ceil($this->quantity / $this->data['craft_quantity']);
            //
            // If this is not a tooltip or extended
            // check for synths
            //
            if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip') || $this->isFlagged('cart'))
            {
                // get synths
                if ($synths = $this->getExtendedRecipe($item['id'], $item['quantity'])) {
                    $item['synths'] = $synths;
                }
            }

            $tree[] = $item;
        }

        return $tree;
    }

    //
    // Recurrsively call the next recipe in the tree
    //
    public function getExtendedRecipe($itemId, $quantity)
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->select('id')->from(ContentDB::RECIPE)->where('item = :id')->bind('id', $itemId);
        $recipes = $dbs->get()->all();
        if (!$recipes) {
            return false;
        }

        // go through each obtained synth and adjust quantities
        foreach($recipes as $synth)
        {
            // synth id
            $id = $synth['id'];

            // Get the synth
            $synth = $this->addRecipe($id, $quantity);

            // increment tree quantities
            $tree = [];
            foreach($synth['tree'] as $i => $item)
            {
                // First quantity multiplication is the quantity for 1 times by the number we need
                //$item['quantity'] = $item['quantity'] * $quantity;

                // Now divide total by the craft quantity, if the new quantity is less than what is required, override.
                $newQuantity = ceil($item['quantity'] / $this->data['craft_quantity']);
                $item['quantity'] = $newQuantity < $item['quantity'] ? $item['quantity'] : $newQuantity;

                //$item['quantity'] = ceil((($item['quantity'] * $quantity)) $this->quantity) / $this->content['craft_quantity']);
                $tree[] = $item;
            }

            // append tree
            $synth['tree'] = $tree;
            $synths[$id] = $synth;
        }

        return $synths;
    }

    //
    // Set some game data
    //
    public function setGameData()
    {
        $this->data['color'] = isset($this->data['item_rarity']) ? ContentDB::$rarity[$this->data['item_rarity']] : null;
        $this->data['stars'] = $this->data['level_diff'];
        $this->data['stars_html'] = '';

        if (isset($this->data['item']['id'])) {
            $this->data['item_url'] = $this->url('item', $this->data['item']['id'], $this->data['name']);
        }

        if ($this->data['stars'] > 0) {
            for ($i=0; $i < $this->data['stars']; $i++) {
                $this->data['stars_html'] .= '&#9733;';
            }
        }

        //$this->attachGameIcons();

        if (isset($this->data['item']['icon'])) {
            $this->data['icon'] = $this->data['item']['icon'];
        }

        // do difficulty, durability and quality factors
        if (isset($this->data['difficulty_factor'])) {
            $this->data['difficulty_factor'] = ($this->data['difficulty_factor'] / 100);
            $this->data['durability_factor'] = ($this->data['durability_factor'] / 100);
            $this->data['quality_factor'] = ($this->data['quality_factor'] / 100);
            $this->data['difficulty'] = floor($this->data['difficulty'] * $this->data['difficulty_factor']);
            $this->data['durability'] = floor($this->data['durability'] * $this->data['durability_factor']);
            $this->data['quality'] = floor($this->data['quality'] * $this->data['quality_factor']);
        }
    }
}
