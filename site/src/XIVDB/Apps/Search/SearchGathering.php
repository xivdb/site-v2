<?php

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

//
// Search gathering
//
class SearchGathering extends Search
{
    protected $Content = 'Gathering';
    protected $ContentTable = ContentDB::GATHERING;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{gathering}' => $this->Content::$search,
                '{gathering_type}' => [ 'name_{lang} as type_name' ],
                '{items}' => [
                    'level_equip as item_level_equip',
                    'level_item as item_level_item',
                    'rarity as rarity',
                    'icon as icon',
                    'icon_lodestone as icon_lodestone',
                ],
            ])
            ->from('{gathering}')
            ->join('{gathering}.gathering_type', '{gathering_type}.id')
            ->join('{gathering}.item', '{items}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // levels
        //
        $this->handleFilters(['level|gt','level|lt'], 'and');
        $this->handleFilters(['level_view|gt','level_view|lt'], 'and');

        //
        // basic filters
        //
        $this->handleFilters([
            'level_diff|et',
            'gathering_type|et',
            'patch|et',
        ], 'and');
    }
}
