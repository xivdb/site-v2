<?php

/**
 * SearchDatabaseEquipment
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseEquipment
{
    protected function getEquipment()
    {
        return
        [
            'weapons' =>
            [
                'title' => $this->lang['weapons'],
                'menu' => $this->getWeapons(),
            ],

            'shields' =>
            [
                'icon' => '/img/ui/shield.png',
                'title' => $this->lang['shields'],
                'url' => $this->builder->one('items')->add('item_ui_category|et', 11)->order('level_item', 'desc')->get(),
            ],

            'tools' =>
            [
                'title' => $this->lang['tools'],
                'menu' => $this->getTools(),
            ],

            'armor' =>
            [
                'title' => $this->lang['armor'],
                'menu' => $this->getArmor(),
            ],

            'ilv' =>
            [
                'title' => $this->lang[587],
                'menu' => $this->getItemLevel(),
            ],

            'rlv' =>
            [
                'title' => $this->lang[616],
                'menu' => $this->getRequiredLevel(),
            ],

            'accessories' =>
            [
                'title' => 'Accessories',
                'menu' => $this->getAccessories(),
            ],

            /*'pvp' =>
            [
                'icon' => '/img/game/060000/060459.png',
                'title' => $this->lang['pvp_gear'],
                'url' => $this->builder->one('items')->ispvp()->order('level_item', 'desc')->get()
            ],*/

            'gc' =>
            [
                'title' => $this->filters['categories'][12]['name'],
                'menu' => $this->getGrandCompany(),
            ],
        ];
    }

    protected function getWeapons()
    {
        $menu =
        [
            'classes' => [ 'title' => 'Classes', 'menu' => [] ],
            'jobs' => [ 'title' => 'Jobs', 'menu' => [] ],
            'other_jobs' => [ 'title' => 'Other Jobs', 'menu' => [] ],
        ];

        // classes
        foreach($this->filters['classes'] as $cj)
        {
            $menu['classes']['menu'][] = array_merge($this->getClassJob($cj), [
                'url' => $this->builder->one('items')->add('classjobs', $cj['id'])
                            ->add('item_ui_kind|et', 1)->order('level_item', 'desc')->get()
            ]);
        }

        // jobs
        foreach($this->filters['jobs'] as $cj)
        {
            $menu['jobs']['menu'][] = array_merge($this->getClassJob($cj), [
                'url' => $this->builder->one('items')->add('classjobs', $cj['id'])
                            ->add('item_ui_kind|et', 1)->order('level_item', 'desc')->get()
            ]);
        }

        // Other jobs
        foreach($this->filters['other_jobs'] as $cj)
        {
            $menu['other_jobs']['menu'][] = array_merge($this->getClassJob($cj), [
                'url' => $this->builder->one('items')->add('classjobs', $cj['id'])
                            ->add('item_ui_kind|et', 1)->order('level_item', 'desc')->get()
            ]);
        }

        return $menu;
    }

    protected function getArmor()
    {
        $menu = [];
        foreach($this->filters['kinds'][2]['categories'] as $value)
        {
            if ($value['id'] == '11') continue;

            $menu[] = [
                'icon' => sprintf('/img/ui/gear/%s.png', str_ireplace(' ', null, strtolower($value['name_en']))),
                'title' => $value['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', $value['id'])->order('level_item', 'desc')->get(),
            ];

        }

        return $menu;
    }

    protected function getAccessories()
    {
        $menu = [];
        foreach($this->filters['kinds'][3]['categories'] as $value)
        {
            $menu[] = [
                'icon' => sprintf('/img/ui/gear/%s.png', str_ireplace(' ', null, strtolower($value['name_en']))),
                'title' => $value['name'],
                'url' => $this->builder->one('items')
                            ->add('item_ui_category|et', $value['id'])->order('level_item', 'desc')->get(),
            ];

        };

        return $menu;
    }

    protected function getItemLevel()
    {
        $menu = [];

        $menu[] = [
            'title' => 'iLv. 1-25',
            'url' => $this->builder->one('items')->add('level_item|gt', 1)->add('level_item|lt', 25)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 25-50',
            'url' => $this->builder->one('items')->add('level_item|gt', 25)->add('level_item|lt', 50)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 50-75',
            'url' => $this->builder->one('items')->add('level_item|gt', 50)->add('level_item|lt', 75)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 75-100',
            'url' => $this->builder->one('items')->add('level_item|gt', 75)->add('level_item|lt', 100)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 100-125',
            'url' => $this->builder->one('items')->add('level_item|gt', 100)->add('level_item|lt', 125)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 125-150',
            'url' => $this->builder->one('items')->add('level_item|gt', 125)->add('level_item|lt', 150)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 150-175',
            'url' => $this->builder->one('items')->add('level_item|gt', 150)->add('level_item|lt', 175)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 175-200',
            'url' => $this->builder->one('items')->add('level_item|gt', 175)->add('level_item|lt', 200)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'iLv. 200+',
            'url' => $this->builder->one('items')->add('level_item|gt', 200)->order('level_item', 'desc')->order('level_item', 'desc')->get(),
        ];

        return $menu;
    }

    protected function getRequiredLevel()
    {
        $menu = [];

        $menu[] = [
            'title' => 'Level 1-10',
            'url' => $this->builder->one('items')->add('level_equip|gt', 1)->add('level_equip|lt', 10)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'Level 10-20',
            'url' => $this->builder->one('items')->add('level_equip|gt', 10)->add('level_equip|lt', 20)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'Level 20-30',
            'url' => $this->builder->one('items')->add('level_equip|gt', 20)->add('level_equip|lt', 30)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'Level 30-40',
            'url' => $this->builder->one('items')->add('level_equip|gt', 30)->add('level_equip|lt', 40)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'Level 40-50',
            'url' => $this->builder->one('items')->add('level_equip|gt', 40)->add('level_equip|lt', 50)->order('level_item', 'desc')->get(),
        ];

        $menu[] = [
            'title' => 'Level 50-60',
            'url' => $this->builder->one('items')->add('level_equip|gt', 50)->add('level_equip|lt', 60)->order('level_item', 'desc')->get(),
        ];


        return $menu;
    }

    protected function getGrandCompany()
    {
        $flags = [
            1 => '/img/game/060000/060590.png',
            2 => '/img/game/060000/060591.png',
            3 => '/img/game/060000/060592.png'
        ];

        $main = [];
        foreach($this->filters['grand_company'] as $id => $value)
        {
            if (!isset($flags[$value['id']])) {
                continue;
            }

            $main[] = [
                'icon' => $flags[$value['id']],
                'title' => $value['name'],
                'url' => $this->builder->one('items')
                            ->add('item_series|et', $value['id'])->order('level_item', 'desc')->get(),
            ];
        }

        return $main;
    }

    protected function getTools()
    {
        $menu =
        [
            'crafting' => [ 'title' => 'Crafting', 'menu' => [] ],
            'gathering' => [ 'title' => 'Gathering', 'menu' => [] ],
        ];

        // classes
        foreach($this->filters['crafting'] as $cj)
        {
            $menu['crafting']['menu'][] = array_merge($this->getClassJob($cj), [
                'url' => $this->builder->one('items')
                            ->add('classjobs', $cj['id'])->add('item_ui_kind|et', 2)->order('level_item', 'desc')->get()
            ]);
        }

        // classes
        foreach($this->filters['gathering'] as $cj)
        {
            $menu['gathering']['menu'][] = array_merge($this->getClassJob($cj), [
                'url' => $this->builder->one('items')
                            ->add('classjobs', $cj['id'])->add('item_ui_kind|et', 2)->order('level_item', 'desc')->get()
            ]);
        }

        return $menu;
    }
}
