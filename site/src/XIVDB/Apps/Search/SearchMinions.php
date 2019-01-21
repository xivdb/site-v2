<?php
/**
 * SearchMinions
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchMinions extends Search
{
    protected $Content = 'Minion';
    protected $ContentTable = ContentDB::MINIONS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{minions}' => $this->Content::$search,
                '{minions_race}' => [ 'name_{lang} as race' ]
            ])
            ->from('{minions}')
            ->join('{minions}.minion_race', '{minions_race}.id');
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
