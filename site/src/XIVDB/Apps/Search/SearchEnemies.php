<?php
/**
 * SearchEnemies
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchEnemies extends Search
{
    protected $Content = 'Enemy';
    protected $ContentTable = ContentDB::ENEMY;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{enemy}' => $this->Content::$search,
            ])
            ->from('{enemy}');
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
