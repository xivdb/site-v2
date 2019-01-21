<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;
use XIVDB\Apps\Site\RedisCache;

//
// AppDev
//
trait AppDev
{
    protected function _dev()
    {
        //
        // devblog
        //
        $this->route('/devblog', 'GET', function(Request $request)
        {
            $this->setLastUrl($request);
            $this->noApi($request);

            $dbs = $this->getModule('database');

            // get dev blog posts
            $dbs->QueryBuilder
                ->select('*', false)
                ->from('site_devblog')
                ->where('published = 1')
                ->order('id', 'desc')->limit(0, 50);

            $blogs = $dbs->get()->all();

            $posts = [];
            foreach($blogs as $b) {
                $posts[$b['id']] = $b;
            }

            $post = $request->get('id') && isset($posts[$request->get('id')]) ? $posts[$request->get('id')] : reset($posts);

            return $this->respond('Pages/devblog.twig', [
                'post' => $post,
                'blogs' => $blogs,
            ]);
        });

        //
        // se devtracker
        //
        $this->route('/devtracker', 'GET', function(Request $request)
        {
            $this->setLastUrl($request);
            $this->noApi($request);

            $home = new \XIVDB\Apps\Site\Home();
            $devtracker = $home->getDevTrackerJson();

            return $this->respond('Pages/devtracker.twig', [
                'devtracker' => $devtracker,
            ]);
        });


        //
        // Tooltips
        //
        $this->route('/dev/tooltips', 'GET', function(Request $request)
        {
            $this->noApi($request);
            return $this->respond('Pages/tooltips.twig');
        });

        //
        // Disable site cache for 24 hours
        //
        $this->route('/dev/nocache', 'GET', function(Request $request)
        {
            $this->noApi($request);
            $expire = TIME_60MINUTES * ($request->get('hours') ? $request->get('hours') : 24);
            @setcookie('cache', 'disable', time()+$expire, '/', COOKIE_DOMAIN);
            die('Cache has been disabled for 24 hours. If you need to re-enable the cache, delete the cookie for this site under the name: cache');
        });

        //
        // Disable site cache for 24 hours
        //
        $this->route('/dev/nomaint', 'GET', function(Request $request)
        {
            $expire = TIME_60MINUTES * ($request->get('hours') ? $request->get('hours') : 24);
            @setcookie('nomaint', 'disable', time()+$expire, '/', COOKIE_DOMAIN);
            die('Maintenance has been by-passed for 24 hours');
        });

        //
        // Disable site cache for 24 hours
        //
        $this->route('/dev/unlock-early-access', 'GET', function(Request $request)
        {
            $expire = TIME_60MINUTES * ($request->get('hours') ? $request->get('hours') : 24);
            @setcookie('nomaint', 'disable', time()+$expire, '/', COOKIE_DOMAIN);
            die('Early Access unlocked for 24 hours');
        });

        //
        // Stats
        //
        $this->route('/dev/stats', 'GET', function(Request $request)
        {
            $this->noApi($request);

            $redis   = new RedisCache();
            $hits    = [];
            $unique  = [];
            $seconds = [];
            $api     = [];
            $hours   = [];
            
            foreach (range(1,365) as $num) {
                $hits['all'][$num]   = $redis->get("_stats_hits_{$num}", true) ?: 0;
                $unique['all'][$num] = $redis->get("_stats_unique_{$num}", true) ?: 0;
                
                foreach(['en','de','fr','ja'] as $lang) {
                    $hits[$lang][$num]   = $redis->get("_stats_hits_{$lang}_{$num}", true) ?: 0;
                    $unique[$lang][$num] = $redis->get("_stats_unique_{$lang}_{$num}", true) ?: 0;
                }
            }
    
            foreach (range(1,365) as $num) {
                $api[$num] = $redis->get("_stats_api_{$num}", true) ?: 0;
            }
            
            foreach (range(0,30) as $num) {
                $time = time() - $num;
                $seconds[$num] = $redis->get("_stats_hits_second_{$time}", true) ?: 0;
            }
    
            foreach (range(0,23) as $num) {
                $hours[$num] = $redis->get("_stats_hits_hour_{$num}", true) ?: 0;
            }
            
            return $this->respond('Pages/stats.twig', [
                'stats' => [
                    'hits'    => $hits,
                    'unique'  => $unique,
                    'seconds' => $seconds,
                    'api'     => $api,
                    'hours'   => $hours,
                ],
            ]);
        });
    }
}
