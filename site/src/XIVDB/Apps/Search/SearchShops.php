<?php
/**
 * SearchShops
 *
 * @version 1.0
 * @author 
 */


/**
 *
 * Removing from search for now, you might as well
 * search the item you're after or the NPC name
 *
 *
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchShops extends Search
{
    protected $Content = 'Shop';
    protected $ContentTable = ContentDB::SHOP;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{shop}' => $this->Content::$search,
                '{npc}' => [ 'name_{lang} as npc_name' ]
            ])
            ->from('{shop}')
            ->join('{shop}.id', '{to_npc_shop}.shop')
            ->join('{to_npc_shop}.npc', '{npc}.id');
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
