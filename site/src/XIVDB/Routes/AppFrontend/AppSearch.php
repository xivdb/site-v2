<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;
use XIVDB\Apps\Search\Search;

//
// AppSearch
//
trait AppSearch
{
    protected function _search()
    {
        // searching
        $this->route('/search', 'GET', function(Request $request)
        {
            // if there is a string, only cache for a short time
            $cacheTimeExpires = $request->get('string') ? SEARCH_CACHE_TIMEOUT_MIN : SEARCH_CACHE_TIMEOUT_MAX;

            // get hash
            $redis = $this->getRedis();
            $key = 'sr_'. $redis->hash($request->query->all());

            // check cache
            if (!$results = $redis->get($key))
            {
                // new search
                $search = new Search();
                $results = $search->setRequest($request)->getResults();

                // cache results
                $redis->set($key, $results, $cacheTimeExpires);
            }

            // if json
            if ($this->isApiRequest($request)) {
                return $this->json($results);
            }

            // print nice stuff and end
            exit('Please use the "api.xivdb.com" route to access the search! More information can be found at: https://github.com/xivdb/api');
        });
    }
}
