<?php

/**
 * SearchDatabaseAchievements
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseAchievements
{
    protected function getAchievements()
    {
        $main = [];
        foreach($this->filters['categories'] as $id => $menu)
        {
            $submenu = [];
            foreach($menu['categories'] as $sm)
            {
                $submenu[] = [
                    'icon' => '/img/ui/default.png',
                    'title' => $sm['name'],
                    'url' => $this->builder->one('achievements')->add('achievement_category|et', $sm['id'])->get()
                ];
            }

            $main[str_ireplace(' ', '_', strtolower($menu['name_en']))] = [
                'icon' => '/img/libra/heavy_dot_blue.png',
                'title' => $menu['name'],
                'menu' => $submenu,
            ];
        }

        return $main;
    }
}
