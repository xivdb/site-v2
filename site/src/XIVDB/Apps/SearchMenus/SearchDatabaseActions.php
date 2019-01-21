<?php

/**
 * SearchDatabaseActions
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseActions
{
    protected function getActions()
    {
        $orderByType = [];
        $typeValues =
        [
            1 => 'Classes',
            2 => 'Crafting',
            3 => 'Gathering',
            4 => 'Jobs',
            5 => 'Other Jobs',
        ];

        foreach($this->filters['classjob'] as $cj)
        {
            $classes    = [1,2,3,4,5,6,7,26,29];
            $crafting   = [8,9,10,11,12,13,14,15];
            $gathering  = [16,17,18];
            $jobs       = [19,20,21,22,23,24.25,27,28,30];
            $otherJobs  = [31,32,33];

            if (in_array($cj['id'], $classes)) {
                $type = 1;
            } else if (in_array($cj['id'], $crafting)) {
                $type = 2;
            } else if (in_array($cj['id'], $gathering)) {
                $type = 3;
            } else if (in_array($cj['id'], $jobs)) {
                $type = 4;
            } else if (in_array($cj['id'], $otherJobs)) {
                $type = 5;
            }

            $orderByType[$type][] = $cj;
        }

        ksort($orderByType);

        $menu = [];
        foreach($orderByType as $type => $list)
        {
            $submenu = [];
            foreach($list as $cj)
            {
                $ids = [ $cj['id'] ];

                // add parent if its not same as current Id
                if ($cj['id'] != $cj['classjob_parent']) {
                    $ids[] = $cj['classjob_parent'];
                }

                $submenu[] = array_merge($this->getClassJob($cj), [
                    'url' => $this->builder
                                    ->one('actions')
                                    ->add('classjobs', implode(',', $ids))
                                    ->add('classjobs_andor', 'or')
                                    ->order('level', 'desc')
                                    ->get()
                ]);
            }

            $menu[str_ireplace(' ', '_', strtolower($typeValues[$type]))] = [
                'title' => $typeValues[$type],
                'menu' => $submenu,
            ];
        }


        //show($menu); die;

        return $menu;
    }
}
