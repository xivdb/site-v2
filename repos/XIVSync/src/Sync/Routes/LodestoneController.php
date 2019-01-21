<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\Request;

trait LodestoneController
{
    /**
     * Parse a lodestone stuff
     */
    protected function _lodestoneParse()
    {
        $this->route('/lodestone/banners', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('banners', LODESTONE_BANNERS);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        $this->route('/lodestone/topics', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('topics', LODESTONE_TOPICS);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        $this->route('/lodestone/notices', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('notices', LODESTONE_NOTICES);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        $this->route('/lodestone/maintenance', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('maintenance', LODESTONE_MAINTENANCE);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        $this->route('/lodestone/updates', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('updates', LODESTONE_UPDATES);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        $this->route('/lodestone/status', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('status', LODESTONE_STATUS);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        $this->route('/lodestone/worldstatus', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('worldstatus', LODESTONE_WORLD_STATUS);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Parse a feast season
         */
        $this->route('/feast/season/{season}', 'GET', function(Request $request, $season)
        {
            if (!$season) {
                die('No season provided');
            }

            $params = [];

            // q param is required, even if empty
            $params[] = sprintf('q=%s', ucwords($request->get('name')));

            // server is optional
            if ($dc = $request->get('dc')) {
                $params[] = sprintf('dcgroup=%s', ucwords($dc));
            }

            // party is optional
            if ($soloOrParty = $request->get('solo_party')) {
                $params[] = sprintf('solo_party=%s', $soloOrParty);
            }

            // get url
            switch($season) {
                case 1:
                    $url = LODESTONE_FEAST_SEASON_1;
                    break;

                case 2:
                    $url = LODESTONE_FEAST_SEASON_2;
                    break;

                case 3:
                    $url = LODESTONE_FEAST_SEASON_3;
                    break;

                case 4:
                default:
                    $url = LODESTONE_FEAST_SEASON_4;
                    break;
            }

            if (!$url) {
                die('No valid season: 1,2,3,4/current');
            }

            // build request
            $params = $params ? '?' . implode('&', $params) : '';
            $url = $url . $params;

            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('feast', $url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Parse deep dungeon
         */
        $this->route('/lodestone/deepdungeon', 'GET', function(Request $request)
        {
            $hash = false;
            switch($request->get('classjob')) {
                case 'gla':
                case 'pld':
                    $hash = '125bf9c1198a3a148377efea9c167726d58fa1a5'; break;

                case 'mar':
                case 'war':
                    $hash = '741ae8622fa496b4f98b040ff03f623bf46d790f'; break;

                case 'drk':
                    $hash = 'c31f30f41ab1562461262daa74b4d374e633a790'; break;

                case 'cnj':
                case 'whm':
                    $hash = '56d60f8dbf527ab9a4f96f2906f044b33e7bd349'; break;

                case 'sch':
                    $hash = '56f91364620add6b8e53c80f0d5d315a246c3b94'; break;

                case 'ast':
                    $hash = 'eb7fb1a2664ede39d2d921e0171a20fa7e57eb2b'; break;

                case 'mnk':
                case 'pug':
                    $hash = '46fcce8b2166c8afb1d76f9e1fa3400427c73203'; break;

                case 'drg':
                case 'lnc':
                    $hash = 'b16807bd2ef49bd57893c56727a8f61cbaeae008'; break;

                case 'nin':
                case 'rog':
                    $hash = 'e8f417ab2afdd9a1e608cb08f4c7a1ae3fe4a441'; break;

                case 'brd':
                case 'arc':
                    $hash = 'f50dbaf7512c54b426b991445ff06a6697f45d2a'; break;

                case 'mch':
                    $hash = '773aae6e524e9a497fe3b09c7084af165bef434d'; break;

                case 'blm':
                case 'thm':
                    $hash = 'f28896f2b4a22b014e3bb85a7f20041452319ff2'; break;

                case 'acn':
                case 'shm':
                    $hash = '9ef51b0f36842b9566f40c5e3de2c55a672e4607'; break;
            }

            $params = [];

            // q param is required, even if empty
            $params[] = sprintf('q=%s', ucwords($request->get('name')));

            // server is optional
            if ($dc = $request->get('dc')) {
                $params[] = sprintf('dcgroup=%s', ucwords($dc));
            }

            // party is optional
            if ($soloOrParty = $request->get('solo_party')) {
                $params[] = sprintf('solo_party=%s', $soloOrParty);
            }

            // party is optional
            if ($hash) {
                $params[] = sprintf('subtype=%s', $hash);
            }

            // build request
            $params = $params ? '?' . implode('&', $params) : '';
            $url = LODESTONE_DEEP_DUNGEON . $params;

            $parser = new \Sync\App\Lodestone();
            $data = $parser->parse('deepdungeon', $url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Parse dev blog PR
         */
        $this->route('/lodestone/devblog', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parseDevBlog(LODESTONE_DEV_BLOG);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Parse dev tracker
         */
        $this->route('/lodestone/devtracker', 'GET', function(Request $request)
        {
            $parser = new \Sync\App\Lodestone();
            $data = $parser->parseDevTracker(LODESTONE_FORUMS_HOMEPAGE);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });
    }
}
