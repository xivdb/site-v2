<?php

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

//
// Handler Trait
//
Trait TraitHandlers
{
    //
    // Handle a filter list
    //
    protected function handleFilters($filters, $split = 'and')
    {
        // filter array
        $filterArray = [];

        // loop through filters
        foreach($filters as $filter)
        {
            // if filter exists in request
            if ($value = $this->request->get($filter))
            {
                // double check
                if (empty($value) || strlen($value) < 1) {
                    continue;
                }

                // get field and symbol
                @list($field, $symbol) = explode('|', $filter);

                // special condition
                $symbol = $symbol ? $this->qb()->getSymbol($symbol) : '>';
                $value = $value && $value === 'true' ? '0' : $value;

                //create bind
                $bind = sprintf(':%s', mt_rand(0,9999999999));

                // add to arr
                $filterArray[] = sprintf('{table}.%s %s %s', $field, $symbol, $bind);

                // bind value
                $this->qb()->bind($bind, $value);
            }
        }

        // append where array
        if ($filterArray) {
            $this->qb()->where($filterArray, $split);
        }
    }

    //
    // Handle page range
    //
    protected function handlePaging($total)
    {
        // page
        $page = $this->request->get('page') ? intval($this->request->get('page')) : 1;
        $page = $page < 1 ? 1 : $page;

        // work out how many pages to generate, then generate them into an array
        $totalPages = ceil($total / $this->limit);

        // if the number of pages is below the max, just return that
        if ($totalPages < SEARCH_PAGE_RANGE) {
            $pages = range(1, $totalPages);
        } else {
            // number of pages to the left and right of the current page
            $leftPage = $page - SEARCH_PAGE_RANGE;
            $rightPage = $page + SEARCH_PAGE_RANGE;

            // make sure we dont go below 1
            if ($leftPage < 1) {
                $leftPage = 1;
            }

            // make sure we dont go higher than total
            if ($rightPage > $totalPages) {
                $rightPage = $totalPages;
            }

            $left = range($leftPage, $page);
            $right = range($page, $rightPage);

            $pages = array_merge($left, $right);
            $pages = array_unique($pages);
            $pages = array_values($pages);

            asort($pages);
        }

        // next page
        $next = ($page + 1) > $totalPages ? $totalPages : $page + 1;
        $prev = ($page - 1) < 1 ? 1 : $page - 1;

        return [
            'page' => $page,
            'total' => $totalPages,
            'pages' => $pages,
            'next' => $next,
            'prev' => $prev,
        ];
    }
}
