<?php
/**
 * SearchActions
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchTraits extends Search
{
    protected $Content = 'Trait';
    protected $ContentTable = ContentDB::TRAITS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{traits}' => $this->Content::$search,
                '{classjob}' => [ 'name_{lang} as class_name' ],
            ])
            ->from('{traits}')
            ->join('{traits}.classjob', '{classjob}.id');
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
            'patch|et',
        ], 'and');

        //
        // class jobs
        //
        $this->classjobs();
    }

    //
    // Handle classjobs
    //
    protected function classjobs()
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
            $where[] = sprintf('({traits}.classjob = %s)', $bind);

            // append where
            $this->qb()->bind($bind, $id);
        }

        // get valid and/or
        $andor = $this->qb()->getAndOr($this->request->get('classjobs_andor'));

        // append where
        $this->qb()->where($where, $andor);
    }
}
