<?php

/**
 * SearchDatabaseOther
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseOther
{
    protected function getOther()
    {
        return
        [
            'misc' => [
                'title' => 'Miscellaneous',
                'menu' => $this->getOtherList(),
            ],

            'pages' => [
                'title' => 'Information',
                'menu' => $this->getPages(),
            ]
        ];
    }

    protected function getAll()
    {
        $language = new \XIVDB\Apps\Site\Language();
        return
        [
            'main' =>
            [
                'items' =>
                [
                    'title' => $language->custom(408),
                    'url' => $this->builder->one('items')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'recipes' =>
                [
                    'title' => $language->custom(224),
                    'url' => $this->builder->one('recipes')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'quests' =>
                [
                    'title' => $language->custom(189),
                    'url' => $this->builder->one('quests')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'fates' =>
                [
                    'title' => $language->custom(404),
                    'url' => $this->builder->one('fates')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'instances' =>
                [
                    'title' => $language->custom(841),
                    'url' => $this->builder->one('instances')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'skills' =>
                [
                    'title' => $language->custom(225),
                    'url' => $this->builder->one('actions')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'achievements' =>
                [
                    'title' => $language->custom(397),
                    'url' => $this->builder->one('achievements')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
                'leves' =>
                [
                    'title' => $language->custom(654),
                    'url' => $this->builder->one('leves')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default.png',
                ],
            ],

            'misc' =>
            [
                'zones' =>
                [
                    'title' => $language->custom(641),
                    'url' => $this->builder->one('places')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'statuses' =>
                [
                    'title' => $language->custom(405),
                    'url' => $this->builder->one('status')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'titles' =>
                [
                    'title' => $language->custom(241),
                    'url' => $this->builder->one('titles')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'npcs' =>
                [
                    'title' => $language->custom(402),
                    'url' => $this->builder->one('npcs')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'enemies' =>
                [
                    'title' => $language->custom(650),
                    'url' => $this->builder->one('enemies')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'minions' =>
                [
                    'title' => $language->custom(446),
                    'url' => $this->builder->one('minions')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'mounts' =>
                [
                    'title' => $language->custom(199),
                    'url' => $this->builder->one('mounts')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'emotes' =>
                [
                    'title' => $language->custom(177),
                    'url' => $this->builder->one('emotes')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
                'weather' =>
                [
                    'title' => $language->custom(242),
                    'url' => $this->builder->one('weather')->order('id', 'desc')->get(),
                    'icon' => '/img/ui/default2.png',
                ],
            ],

            'special' =>
            [
                'characters' =>
                [
                    'title' => $language->custom(33),
                    'url' => $this->builder->one('characters')->order('name', 'asc')->get(),
                    'icon' => '/img/ui/default3.png',
                ]
            ]
        ];
    }

    protected function getPages()
    {
        return [
            'cabinet' => [
                'title' => 'Armoire',
                'url' => '/data/armoire',
            ]
        ];
    }

    protected function getOtherList()
    {
        return
        [
            'titles' =>
            [
                'title' => 'Titles',
                'url' => $this->builder->one('titles')->order('id', 'desc')->get(),
                'icon' => '/img/ui/title.png',
            ],

            'emotes' =>
            [
                'title' => 'Emotes',
                'url' => $this->builder->one('emotes')->order('id', 'desc')->get(),
                'icon' => '/img/game/064000/064114.png',
                'round' => true,
            ],

            'weather' =>
            [
                'title' => 'Weather',
                'url' => $this->builder->one('weather')->order('id', 'desc')->get(),
                'icon' => '/img/game/060000/060202.png',
            ],

            'npc' =>
            [
                'title' => 'NPCs',
                'url' => null,
                'icon' => '/img/game/060000/060768.png',
                'url' => $this->builder->one('npcs')->get()
            ],

            'minions' =>
            [
                'title' => 'Minions',
                'url' => $this->builder->one('minions')->order('id', 'desc')->get(),
                'icon' => '/img/ui/minion.png',
                'round' => true,
            ],

            'mounts' =>
            [
                'title' => 'Mounts',
                'url' => $this->builder->one('mounts')->order('id', 'desc')->get(),
                'icon' => '/img/ui/mount.png',
                'round' => true,
            ],
        ];
    }
}
