<?php

/**
 * SearchDatabase
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseHnL
{
    protected function getHnL()
    {
        return
        [
            'crafting' =>
            [
                'title' => 'Crafting Recipes',
                'menu' => $this->getCrafting(),
            ],

            'gathering' =>
            [
                'title' => 'Gathering',
                'menu' => $this->getGathering(),
            ],
        ];
    }

    protected function getCrafting()
    {
        $menu = [];
        foreach($this->filters['crafting'] as $cj)
        {
            $menu[] = array_merge($this->getClassJob($cj), [
                'url' => $this->builder->one('recipes')->add('classjobs', $cj['id'])->order('level_view', 'desc')->get()
            ]);
        }

        return $menu;
    }

    protected function getGathering()
    {
        $menu = [];
        foreach($this->filters['gatheringtypes'] as $type)
        {
            $iconList = [
                1 => 60437,
                2 => 60433,
                3 => 60432,
                4 => 60930,
                0 => 60438,
            ];

            $menu[] = [
                'icon' => $this->iconize($iconList[$type['id']]),
                'title' => $type['name'],
                'url' => $this->builder->one('gathering')->add('gathering_type', $type['id'])->get()
            ];
        }

        return $menu;
    }
}
