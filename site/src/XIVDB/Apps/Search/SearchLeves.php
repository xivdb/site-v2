<?php
/**
 * SearchLeves
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchLeves extends Search
{
    protected $Content = 'Leve';
    protected $ContentTable = ContentDB::LEVES;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{leves}' => $this->Content::$search,
                '{leves_assignment}' => [
		            'name_{lang} as assignment_type_name',
		            'icon as assignment_type_icon'
		        ],
            ])
            ->from('{leves}')
            ->join('{leves}.leve_assignment_type', '{leves_assignment}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // class_level
        //
        $this->handleFilters(['class_level|gt','class_level|lt'], 'and');

        //
        // basic filters
        //
        $this->handleFilters([
            'placename|et',
            'patch|et',
        ]);
    }
}
