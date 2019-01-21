<?php

/**
 * SearchPatches
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchPatches
{
    public function getPatchesMenu()
    {
        // return
        return $this->getPatchList();
    }

    private function getPatchList()
    {
        // build menu
        $menu = [];
        foreach($this->filters['patches'] as $i => $patch)
        {
            $string = sprintf('patch+%s', $patch['command']);
            $version = explode('.', $patch['command'])[0];

            $temp = [
                'icon' => '/img/ui/default.png',
                'title' => sprintf('<em class="yellow">%s</em> %s', $patch['command'], $patch['name']),
                'url' => $this->builder->string($string),
            ];

            $temp['icon'] = ($patch['is_expansion']) ? '/img/ui/default3.png' : $temp['icon'];
            $temp['icon'] = ($i == 0) ? '/img/ui/star.png' : $temp['icon'];

            $menu[$version][] = $temp;
        }

        return $menu;
    }
}
