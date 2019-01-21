<?php

/**
 * SearchDatabaseEorzea
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseEorzea
{
    protected function getEorzea()
    {
        return
        [
            'npc' =>
            [
                'title' => 'NPCs',
                'icon' => '/img/ui/npc.png',
                'url' => $this->builder->one('npcs')->order('id', 'desc')->get()
            ],

            'minions' =>
            [
                'title' => 'Minions',
                'icon' => '/img/ui/minion.png',
                'url' => $this->builder->one('minions')->order('id', 'desc')->get()
            ],

            'mounts' =>
            [
                'title' => 'Mounts',
                'icon' => '/img/ui/mount.png',
                'url' => $this->builder->one('mounts')->order('id', 'desc')->get(),
            ],
        ];
    }
}
