<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\Request;

trait FrontendController
{
    protected function _home()
    {
        /**
         * Get data
         */
        $this->route('/', 'GET', function(Request $request)
        {
            if ($request->get('json')) {
                return $this->json($this->getStats());
            }

            return $this->respond('Home/index.html.twig', $this->getStats());
        });

        /**
         * Toggle maintenance
         */
        $this->route('/maintenance', 'GET', function(Request $request)
        {
            $this->locked($request);

            if (!$request->get('set')) {
                die('No set state');
            }

            $state = ($request->get('set') == 'on') ? 1 : 0;
            file_put_contents(ROOT .'/maintenance.status', intval($state));

            return $this->json([
                'status' => $state ? 'Maintenance enabled' : 'Maintenance disabled',
            ]);
        });

        /**
         * Show logging
         */
        $this->route('/logging', 'GET', function(Request $request)
        {
            $this->locked($request);
            $logging = file_get_contents(ROOT .'/logging');
            $logging = explode("\n", trim($logging));
            return $this->json($logging);
        });
    }

    /**
     * Get stats
     * @return array
     */
    private function getStats()
    {
        $cachetime = file_exists(STATS_JSON) ? filemtime(STATS_JSON) : 0;

        if ($cachetime < (time()-TIME_60SECONDS)) {
            $basic = new \Sync\App\Basic();
            $stats = $basic->getUpdateStatistics();
            file_put_contents(STATS_JSON, json_encode($stats));
            $cached = false;
        } else {
            $stats = file_get_contents(STATS_JSON);
            $stats = json_decode($stats, true);
            $cached = true;
        }

        $data = [
            'stats' => $stats,
            'cached' => $cached,
            'cachetime' => $cachetime,
        ];

        return $data;
    }
}
