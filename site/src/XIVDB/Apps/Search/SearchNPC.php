<?php
/**
 * SearchNPC
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchNPC extends Search
{
    protected $Content = 'NPC';
    protected $ContentTable = ContentDB::NPC;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{npc}' => $this->Content::$search,
            ])
            ->from('{npc}')
            ->where('{npc}.is_dev = 0');
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
