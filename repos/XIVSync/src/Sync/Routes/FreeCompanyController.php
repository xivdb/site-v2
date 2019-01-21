<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class FreeCompanyController
 * @package Sync\Routes
 */
trait FreeCompanyController
{
    /**
     * Free Company
     */
    protected function _freeCompanyParse()
    {
        /**
         * Get a single FC
         */
        $this->route('/freecompany/parse/{id}', 'GET', function(Request $request, $id)
        {
            $url = sprintf(LODESTONE_FREECOMPANY_URL, $id);
            $parser = new \Sync\App\FreeCompany();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Get a single FC
         */
        $this->route('/freecompany/parse/{id}/members', 'GET', function(Request $request, $id)
        {
            $params = [];

            // page is optional
            if ($page = $request->get('page')) {
                $params[] = sprintf('page=%s', intval($page));
            }

            // build request
            $params = $params ? '?' . implode('&', $params) : '';
            $url = sprintf(LODESTONE_FREECOMPANY_MEMBERS_URL, $id) . $params;
            $parser = new \Sync\App\FreeCompanyMembers();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Search FCs
         */
        $this->route('/freecompany/search', 'GET', function(Request $request)
        {
            $params = [];

            // q param is required, even if empty
            $params[] = sprintf('q=%s', ucwords($request->get('name')));

            // server is optional
            if ($server = $request->get('server')) {
                $params[] = sprintf('worldname=%s', ucwords($server));
            }

            // page is optional
            if ($page = $request->get('page')) {
                $params[] = sprintf('page=%s', intval($page));
            }

            // build request
            $params = $params ? '?' . implode('&', $params) : '';
            $url = LODESTONE_FREECOMPANY_SEARCH_URL . $params;

            $search = new \Sync\App\Search();;
            $data = $search->parseFreeCompanySearch($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });
	}
}
