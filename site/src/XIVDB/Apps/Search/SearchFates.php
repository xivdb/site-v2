<?php
/**
 * SearchFates
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchFates extends Search
{
    protected $Content = 'Fate';
    protected $ContentTable = ContentDB::FATES;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{fates}' => $this->Content::$search,
                '{placenames}' => [ 'name_{lang} as placename' ],
            ])
            ->from('{fates}')
            ->join('{fates}.placename', '{placenames}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // class level
        //
        $this->handleFilters(['class_level|gt','class_level|lt'], 'and');
        $this->handleFilters(['class_level_max|gt','class_level_max|lt'], 'and');

        //
        // basic filters
        //
        $this->handleFilters([
            'placename|et',
            'patch|et',
        ], 'and');
    }
}
