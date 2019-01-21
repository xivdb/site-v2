<?php
/**
 * SearchActions
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchActions extends Search
{
    protected $Content = 'Action';
    protected $ContentTable = ContentDB::ACTIONS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{actions}' => $this->Content::$search,
                '{classjob}' => [ 'name_{lang} as class_name' ],
            ])
            ->from('{actions}')
            ->join('{actions}.classjob', '{classjob}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // levels
        //
        $this->handleFilters([
            'level|gt',
            'level|lt',
        ]);

        //
        // basic filters
        //
        $this->handleFilters([
            'action_category|et',
            'can_target_dead',
            'is_target_area',
            'can_target_self',
            'can_target_party',
            'can_target_friendly',
            'can_target_hostile',
            'is_pvp',
            'patch|et',
        ], 'and');

        //
        // class jobs
        //
        $this->filterClassJobs();
    }

    //
    // Handle classjobs
    //
    protected function filterClassJobs()
    {
        // get attributes
        $classjobs = $this->request->get('classjobs');
        $classjobs = array_filter(explode(',', $classjobs));
        if (!$classjobs || empty($classjobs)) {
            return;
        }

        // loop through class jobs
        $where = [];
        foreach($classjobs as $i => $id)
        {
            $bind = ':cj'. $i;

            // where query
            $where[] = sprintf('({actions}.classjob = %s)', $bind);

            // append where
            $this->qb()->bind($bind, $id);
        }

        // get valid and/or
        $andor = $this->qb()->getAndOr($this->request->get('classjobs_andor'));

        // append where
        $this->qb()->where($where, $andor);
    }
}
