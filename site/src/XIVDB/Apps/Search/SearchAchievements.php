<?php
namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

//
// Search achievements
//
class SearchAchievements extends Search
{
    protected $Content = 'Achievement';
    protected $ContentTable = ContentDB::ACHIEVEMENTS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{achievements}' => $this->Content::$search,
                '{achievements_category}' => [ 'name_{lang} as category_name' ],
                '{achievements_kind}' => [ 'name_{lang} as kind_name' ],
            ])
            ->from('{achievements}')
            ->join('{achievements}.achievement_category', '{achievements_category}.id')
            ->join('{achievements_category}.achievement_kind', '{achievements_kind}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // basic filters
        //
        $this->handleFilters([
            'points|et',
            'achievement_category|et',
            'item',
            'title',
            'patch|et',
        ], 'and');
    }
}
