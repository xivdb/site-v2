<?php
/**
 * SearchPlacenames
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchPlacenames extends Search
{
    protected $Content = 'Placename';
    protected $ContentTable = ContentDB::PLACENAMES;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{placenames}' => $this->Content::$search,
                'region' => [ 'name_{lang} as region_name' ],
            ])
            ->from('{placenames}')
            ->join('{placenames}.region', '{placenames}.id', 'region');
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
