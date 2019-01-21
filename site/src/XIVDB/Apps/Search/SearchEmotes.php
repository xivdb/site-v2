<?php
/**
 * SearchEmotes
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchEmotes extends Search
{
    protected $Content = 'Emote';
    protected $ContentTable = ContentDB::EMOTE;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{emote}' => $this->Content::$search,
            ])
            ->from('{emote}');
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
