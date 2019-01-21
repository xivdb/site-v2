<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class LinkshellController
 * @package Sync\Routes
 */
trait LinkshellController
{
    /**
     * Free Company
     */
    protected function _linkshellParse()
    {
        /**
         * Get a single FC
         */
        $this->route('/linkshell/parse/{id}', 'GET', function(Request $request, $id)
        {
            $url = sprintf(LODESTONE_LINKSHELL_MEMBERS_URL, $id);
            $parser = new \Sync\App\Linkshell();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Search FCs
         */
        $this->route('/linkshell/search', 'GET', function(Request $request)
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
            $url = LODESTONE_LINKSHELL_SEARCH_URL . $params;

            $search = new \Sync\App\Search();
            $data = $search->parseLinkshellSearch($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });
	}
}
