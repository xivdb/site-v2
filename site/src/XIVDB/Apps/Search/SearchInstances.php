<?php
/**
 * SearchInstances
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchInstances extends Search
{
    protected $Content = 'Instance';
    protected $ContentTable = ContentDB::INSTANCES;

    //
    // Base query that fetches the main table
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{instances}' => $this->Content::$search,
                '{content_type}' => [
		            'name_{lang} as content_name',
		            'icon as content_icon',
		            'icon_mini as content_icon_mini',
		        ],
            ])
            ->from('{instances}')
            ->join('{instances}.instance_content_type', '{instances_type}.id')
            ->join('{instances_type}.content_type', '{content_type}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        $this->handleFilters(['level|gt','level|lt'], 'and');
        $this->handleFilters(['level_sync|gt','level_sync|lt'], 'and');
        $this->handleFilters(['item_level|gt','item_level|lt'], 'and');
        $this->handleFilters(['item_level_sync|gt','item_level_sync|lt'], 'and');

        //
        // basic filters
        //
        $this->handleFilters([
            'content_type|et',
            'content_roulette|et',

            'is_echo_default',
            'is_echo_annihilation',
            'content_roulette',
            'alliance',
            'is_in_duty_finder',
            'patch|et',
        ]);
    }
}
