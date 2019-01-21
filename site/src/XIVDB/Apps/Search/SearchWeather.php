<?php
/**
 * SearchWeather
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchWeather extends Search
{
    protected $Content = 'Weather';
    protected $ContentTable = ContentDB::WEATHER;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{weather}' => $this->Content::$search,
            ])
            ->from('{weather}');
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
