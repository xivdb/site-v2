<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppDefault
//
trait AppDefault
{
    protected function _default()
    {
        /**
         * Home page
         */
        $this->route('/', 'GET|POST', function(Request $request)
        {
            $this->setLastUrl($request);
            $this->noApi($request);
            $this->checkV1Redirect($request);

            // get search and filters
            $search = strip_tags($request->get('search'));
            $search = str_ireplace('"', '\"', $search);
            $filters = $request->get('filters');
            $filters = str_ireplace(' ', '+', $filters);

            // if base 64 enabled
            if (SEARCH_BASE64) {
                // encode < unescape < base64
                $filters = urldecode(rawurlencode(base64_decode($filters)));
            }

            // cache key
            $redis = $this->getRedis();
            if (!$data = $redis->get('homepage'))
            {
                // home
                $home = new \XIVDB\Apps\Site\Home();

                // get home page data
                $data = [
                    'lodestone' => $home->getLodestone(),
                    'devtracker' => $home->getDevTrackerJson(),
                    'screenshots' => $home->getScreenshots(),
                    'comments' => $home->getComments(),
					'devblog' => $home->getLatestDevBlog(),
                    'synctimes' => $home->getSyncTimes(),
                ];

                // change time and set if it is recent.
                $fiveDaysAgo = mktime(0, 0, 0, date('m'), date('d') - 5, date('Y'));
                $devblogDate = strtotime($data['devblog']['updated']);
                $data['devblog']['recent'] = ($devblogDate > $fiveDaysAgo) ? true : false;
                $data['devblog']['time'] = $this->getModule('moment', $data['devblog']['updated'])->fromNow()->getRelative();

                $redis->set('homepage', $data, CACHE_HOMEPAGE);
            }

            // passed search data
            $data['search'] = [
                'search' => $search,
                'filters' => $filters,
            ];

            $data['patch_lockdown'] = false;

            if (HIDDEN_PATCH) {
                $dbs = $this->getDatabase();
                $dbs->QueryBuilder
                    ->select(['patch', 'command', 'patch_url', 'name_{lang} as name'])
                    ->from('db_patch')
                    ->limit(0,1);

                if (HIDDEN_PATCH && time() < HIDDEN_PATCH_REMOVAL) {
                    $dbs->QueryBuilder->where('patch = '. HIDDEN_PATCH);
                }

                $data['patch_lockdown'] = $dbs->get()->one();
            }

            return $this->respond('Home/index.twig', $data);
        });

        /**
         * Switch to desktop view
         */
        $this->route('/desktop', 'GET', function(Request $request) {
            $this->noApi($request);
            $this->getModule('users')->setCookieMisc('nomobile', '1');
            return $this->redirect('/');
        });

        /**
         * Switch to mobile view
         */
        $this->route('/mobile', 'GET', function(Request $request) {
            $this->noApi($request);
            $this->getModule('users')->setCookieMisc('nomobile', '0');
            return $this->redirect('/');
        });

        //
        // Translations
        //
        $this->route('/translations', 'GET', function(Request $request)
        {
            $this->setLastUrl($request);
            $this->noApi($request);
            return $this->respond('Pages/translations.twig');
        });

        //
        // About
        //
        $this->route('/about', 'GET', function(Request $request)
        {
            $this->setLastUrl($request);
            $this->noApi($request);

            $dbs = $this->getModule('database');
            $dbs->QueryBuilder
                ->select('*', false)
                ->from('members')
                ->where('star = 1');

            $stars = $dbs->get()->all();
            return $this->respond('Pages/about.twig', [
                'stars' => $stars,
            ]);
        });

        //
        // Support
        //
        $this->route('/support', 'GET', function(Request $request)
        {
            $this->noApi($request);
            return $this->respond('Pages/support.twig');
        });

        //
        // Removal of header ad
        //
        $this->route('/ads/remove', 'GET', function(Request $request) {
            $this->noApi($request);
            @setcookie(COOKIE_ADS_REMOVE, '1', time()+COOKIE_ADS_REMOVE_TIME, '/', COOKIE_DOMAIN);
            return $this->json('ok');
        });

        /**
         * Switch to desktop view
         */
        $this->route('/heart-beat', 'GET', function(Request $request) {
            return $this->json(microtime(true));
        });
    }
}
