<?php
/**
 * SearchTitles
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchTitles extends Search
{
    protected $Content = 'Title';
    protected $ContentTable = ContentDB::TITLES;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{titles}' => $this->Content::$search,
            ])
            ->from('{titles}');
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
            'patch|et',
        ]);
    }
}
