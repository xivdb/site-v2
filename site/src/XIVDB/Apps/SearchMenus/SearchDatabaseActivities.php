<?php

/**
 * SearchDatabaseActivities
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabaseActivities
{
    protected function getActivities()
    {
        return
        [
            'adventure' =>
            [
                'title' => 'Adventure',
                'menu' => $this->getAdventure(),
            ],

            'battle' =>
            [
                'title' => 'Battles',
                'menu' => $this->getBattles(),
            ],
        ];
    }

    protected function getAdventure()
    {
        $language = new \XIVDB\Apps\Site\Language();

        return
        [
            [
                # Main Scenario Quests
                'icon' => '/img/game/061000/061412.png',
                'title' => $this->filters['genres'][1]['name'],
                'url' => $this->builder->one('quests')->add('journal_genre|et', 1)->get()
            ],[
                # Seventh Astral Era Main Scenario Quests
                'icon' => '/img/game/061000/061412.png',
                'title' => $this->filters['genres'][2]['name'],
                'url' => $this->builder->one('quests')->add('journal_genre|et', 2)->get()
            ],[
                # Heavensward Main Scenario Quests
                'icon' => '/img/game/061000/061412.png',
                'title' => $this->filters['genres'][3]['name'],
                'url' => $this->builder->one('quests')->add('journal_genre|et', 3)->get()
            ],[
                # Dragonsong Main Scenario Quests
                'icon' => '/img/game/061000/061412.png',
                'title' => $this->filters['genres'][4]['name'],
                'url' => $this->builder->one('quests')->add('journal_genre|et', 4)->get()
            ],[
                # Post-Dragonsong Main Scenario Quests
                'icon' => '/img/game/061000/061412.png',
                'title' => $this->filters['genres'][5]['name'],
                'url' => $this->builder->one('quests')->add('journal_genre|et', 5)->get()
            ],[
                # Side Story Quests
                'icon' => '/img/game/061000/061412.png',
                'title' => $this->filters['genres'][6]['name'],
                'url' => $this->builder->one('quests')->add('journal_genre|et', 6)->get()
            ]
        ];
    }

    protected function getBattles()
    {
        return
        [
            [
                # Levequest
                'icon' => '/img/game/071000/071041.png',
                'title' => $this->filters['categories'][8]['categories'][1]['name'],
                'url' => $this->builder->one('leves')->get()
            ],[
                # FATE
                'icon' => '/img/game/060000/060458.png',
                'title' => 'F.A.T.E',
                'url' => $this->builder->one('fates')->get()
            ],[
                # dungeons
                'icon' => '/img/game/061000/061801.png',
                'title' => $this->filters['categories'][1]['categories'][1]['name'],
                'round' => true,
                'url' => $this->builder->one('instances')->add('instance_content_type|et', 2)->get()
            ],[
                # raids
                'icon' => '/img/game/061000/061802.png',
                'title' => $this->filters['categories'][1]['categories'][3]['name'],
                'round' => true,
                'url' => $this->builder->one('instances')->add('instance_content_type|et', 5)->get()
            ],[
                # guildhests
                'icon' => '/img/game/061000/061803.png',
                'title' => $this->filters['contenttype'][1]['name'],
                'round' => true,
                'url' => $this->builder->one('instances')->add('instance_content_type|et', 3)->get()
            ],[
                # trails
                'icon' => '/img/game/061000/061804.png',
                'title' => $this->filters['categories'][1]['categories'][2]['name'],
                'round' => true,
                'url' => $this->builder->one('instances')->add('instance_content_type|et', 4)->get()
            ],[
                # pvp
                'icon' => '/img/game/061000/061806.png',
                'title' => $this->filters['categories'][2]['name'],
                'round' => true,
                'url' => $this->builder->one('instances')->add('instance_content_type|et', 6)->get()
            ],[
                # enemies
                'icon' => '/img/game/061000/061710.png',
                'title' => 'Enemies',
                'url' => $this->builder->one('enemies')->get()
            ]/*,[
                # hunting log
                'icon' => '/img/game/060000/060002.png',
                'title' => 'Hunting Log',
            ]*/
        ];
    }
}
