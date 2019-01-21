<?php
/**
 * SearchMounts
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchMounts extends Search
{
    protected $Content = 'Mount';
    protected $ContentTable = ContentDB::MOUNTS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{mounts}' => $this->Content::$search,
            ])
            ->from('{mounts}');
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
            'can_fly|et',
            'patch|et',
        ]);
    }
}
