<?php
/**
 * SearchStatus
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchStatus extends Search
{
    protected $Content = 'Status';
    protected $ContentTable = ContentDB::STATUS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{status}' => $this->Content::$search,
            ])
            ->from('{status}');
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
