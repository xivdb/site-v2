<?php

/**
 * SearchDatabase
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseItems
{
    protected function getItems()
    {
        return
        [
            'materials' =>
            [
                'title' => 'Materials',
                'menu' => $this->getMaterials(),
            ],

            'misc' =>
            [
                'title' => 'Misc',
                'menu' => $this->getMisc(),
            ],

            'airship' =>
            [
                'title' => 'Airship',
                'menu' => $this->getAirship(),
            ],

            'housing' =>
            [
                'title' => 'Housing',
                'menu' => $this->getHousing(),
            ],
        ];
    }

    protected function getHousing()
    {
        $menu = [];
        $temp = [];

        // add furnishing
        $menu[] = [
            'icon' => '/img/ui/housing2.png',
            'title' =>  $this->filters['kinds'][6]['categories'][20]['name'],
            'url' => $this->builder->one('items')
                        ->add('item_ui_category|et', 57)->order('level_item', 'desc')->get(),
        ];

        // different menus
        foreach($this->filters['kinds'][6]['categories'] as $value)
        {
            if (!in_array($value['id'], [64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,82])) continue;

            $temp[] = [
                'icon' => '/img/ui/housing.png',
                'title' => $value['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', $value['id'])->order('level_item', 'desc')->get(),
            ];
        };

        // sort a-z
        $this->sksort($temp, 'title', true);
        return array_merge($menu, $temp);
    }

    protected function getAirship()
    {
        $menu = [];
        foreach($this->filters['kinds'][5]['categories'] as $value)
        {
            if (!in_array($value['id'], [90,91,92,93])) continue;

            $menu[] = [
                'icon' => '/img/ui/airship.png',
                'title' => $value['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', $value['id'])->order('level_item', 'desc')->get(),
            ];
        };

        return $menu;
    }

    protected function getMisc()
    {
        return
        [
            [
                // Food & Drink
                'icon' => '/img/ui/food.png',
                'title' => $this->lang['food_drink'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', 46)->order('level_item', 'desc')->get(),
            ],[
                // potions
                'icon' => '/img/ui/potion.png',
                'title' => $this->filters['kinds'][4]['categories'][0]['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', 44)->order('level_item', 'desc')->get(),
            ],[
                // materia
                'icon' => '/img/ui/materia.png',
                'title' => $this->filters['kinds'][6]['categories'][0]['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', 58)->order('level_item', 'desc')->get(),
            ],[
                // fishing tackle
                'icon' => '/img/game/060000/060465.png',
                'title' => $this->filters['kinds'][6]['categories'][28]['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', 33)->order('level_item', 'desc')->get(),
            ],[
                // Triple Triad Cards
                'icon' => '/img/ui/cards.png',
                'title' => $this->filters['kinds'][6]['categories'][27]['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', 86)->order('level_item', 'desc')->get(),
            ]
        ];
    }

    protected function getMaterials()
    {
        $menu = [];
        foreach($this->filters['kinds'][5]['categories'] as $value)
        {
            if (in_array($value['id'], [90,91,92,93])) continue;

            $menu[] = [
                'icon' => '/img/game/060000/060416.png',
                'title' => $value['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', $value['id'])->order('level_item', 'desc')->get(),
            ];

        };

        return $menu;
    }
}
