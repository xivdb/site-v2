<?php

namespace XIVDB\Routes\Listeners;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    XIVDB\Apps\Search\Filters,
    XIVDB\Apps\SearchMenus\SearchMenus;
use XIVDB\Apps\Site\Cookie;
use XIVDB\Apps\Site\RedisCache;

//
// EventListener
//
trait EventListener
{
    protected function registerEventListeners()
    {
        // attach before listener
        $this->SilexApplication->before(function (Request $request)
        {
            $language = new \XIVDB\Apps\Site\Language();

            // languages to provide
            $languages = $language->getAll();
            $this->addGlobal('languages', $languages);

            // filters and menus
            $filters = (new Filters())->get();
            $menu = (new SearchMenus())->setFilters($filters)->get();
            $this->addGlobal('filters', $filters);
            $this->addGlobal('menu', $menu);

            // language select url
            $urls = $this->getUrls($request);
            $this->addGlobal('urls', $urls);

            // custom defines
            $defines = $this->getDefines();
            $this->addGlobal('defines', $defines);

            // device check
            $this->addGlobal('devices', $this->getDevices());
            $this->addGlobal('nomobile', $this->getModule('users')->getCookie('nomobile'));

            // add current user
            $user = $this->getUser();
            if ($user) {
                $user->setLastOnline();
            }

            $this->addGlobal('user', $user);

            // Cookies
            $this->addGlobal('cookies', [
                'adremove' => @$_COOKIE[COOKIE_ADS_REMOVE],
            ]);

            // add current user
            $this->addGlobal('session', $this->getModule('session')->getAll());

            // add filter (sum an array)
            $this->addFilter(new \Twig_SimpleFilter('sum', function ($array) {
                return array_sum($array);
            }));

            // add filter (md5 hash a string, used often for unique ID's on doms)
            $this->addFilter(new \Twig_SimpleFilter('md5', function ($string) {
                return md5($string);
            }));

            // add filter (new lines to paragraphs)
            $this->addFilter(new \Twig_SimpleFilter('nl2pa', function ($string) {
                $arr = explode("\n",$string);
                $out = '';

                for ($i = 0; $i < count($arr); $i++) {
                    if (strlen(trim($arr[$i])) > 0) {
                        $out .= '<p>'. trim($arr[$i]) .'</p>';
                    }
                }
                return $out;
            }));

            // add a base 64 encode filter
            $this->addFilter(new \Twig_SimpleFilter('base64encode', function ($string) {
                return base64_encode($string);
            }));


            // allow moment
            $this->addFilter(new \Twig_SimpleFilter('moment', function ($unixtime, $format = 'Y, F jS')
            {
                // if its a number, its a unix timestamp
                if (!is_numeric($unixtime)) {
                    $unixtime = strtotime($unixtime);
                }

                if ($unixtime < 0) {
                    return '-';
                }

                // create moment
                $moment = $this->getModule('moment', '@'. $unixtime);

                // which kind of display to use (within 30 days = relative)
                $timedisplay = ($unixtime > (time() - TIME_30DAYS))
                    ? $moment->fromNow()->getRelative()
                    : $moment->format($format);

                return $timedisplay;
            }));

            // month number to month name
            $this->addFilter(new \Twig_SimpleFilter('monthname', function ($number) {
                // Should translate these
                $arr = [null, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October','November', 'December'];
                return isset($arr[$number]) ? $arr[$number] : 'Invalid Month: '. $number;
            }));
            
            
            //
            // June 2018 - tracking
            //
    
            try {
                $host = $request->getHost();
                $host = explode('.', $host);
    
                $key = date('z');
                $hour = date('G');
                $redis = new RedisCache();
    
                if (trim($host[0]) !== 'api') {
                    $redis->increment('_stats_hits_' . $key);
                    $redis->increment('_stats_hits_' . LANGUAGE . '_' . $key);
        
                    // per second
                    @$redis->increment('_stats_hits_second_' . time());
                    @$redis->delete('_stats_hits_second_' . time() - 30);
                    
                    
        
                    $cookie = new Cookie();
                    if (!$cookie->get('hit') || $cookie->get('hit') !== $key) {
                        $cookie->add('hit', $key);
                        $redis->increment('_stats_unique_' . $key);
                        $redis->increment('_stats_unique_' . LANGUAGE . '_' . $key);
                    }
    
                    // delete all hour stats on a new day
                    if (!$redis->get('_stats_day', true)) {
                        // new day, delete all hour stats
                        foreach (range(0,24) as $num) {
                            @$redis->delete('_stats_hits_hour_' . $num);
                        }
                        
                        $redis->set('_stats_day', $key, (60*60*24), true);
                    }
                    
                    $redis->increment('_stats_hits_hour_'. $hour);
                } else {
                    $redis->increment('_stats_api_' . $key);
                }
            } catch (\Exception $ex) {
                // do nothing
            }
        });

        // attach after listener
        $this->SilexApplication->after(function(Request $request, Response $response)
        {
            if ($response instanceof JsonResponse && $request->get('pretty')) {
                $response->setEncodingOptions(JSON_PRETTY_PRINT);
            }

            return $response;
        });

        // attach finish listener
        $this->SilexApplication->finish(function (Request $request)
        {
            if (SITE_MONITOR) {
                // record duration
                (new \XIVDB\Apps\Site\Tracking())->finish('init', $request->getUri());
            }
        });
    }

    //
    // Get system info
    //
    private function getSystemInfo()
    {
        return [
            'memory_get_peak_usage' => $this->convertSize(memory_get_peak_usage(true)),
            'memory_get_usage' => $this->convertSize(memory_get_usage(true)),
            'memory_limit' => ini_get('memory_limit'),
        ];
    }

    //
    // Get a list of defines to pass
    //
    private function getDefines()
    {
        return [
            'DEV' => DEV,
            'URL' => URL,
            'API' => API,
            'XIVSYNC' => XIVSYNC,
            'VERSION' => VERSION,
            'SECURE' => SECURE,
            'LANGUAGE' => LANGUAGE,
            'TRACKING' => TRACKING,
            'META_SITE' => META_SITE,
            'META_TITLE' => META_TITLE,
            'META_AUTHOR' => META_AUTHOR,
            'META_DESC' => META_DESC,
            'META_TYPE' => META_TYPE,
            'META_AUDIENCE' => META_AUDIENCE,
            'META_ROBOTS' => META_ROBOTS,
            'RECAPTCHA_SITE' => RECAPTCHA_SITE,
            'CURRENT_PATCH' => CURRENT_PATCH,
            'PREVIOUS_PATCH' => PREVIOUS_PATCH,
            'DATE_MYSQL' => DATE_MYSQL,
            'DATE_FULL' => DATE_FULL,
            'SEARCH_BASE64' => SEARCH_BASE64,
            'SEARCH_STRING_LENGTH' => SEARCH_STRING_LENGTH,
            'SITE_UNDER_MAINTENANCE' => defined('SITE_UNDER_MAINTENANCE') ? SITE_UNDER_MAINTENANCE : false,
            'GAME_MAX_LEVEL' => GAME_MAX_LEVEL,
            'SITE_MONITOR' => SITE_MONITOR,
            'HIDDEN_PATCH' => HIDDEN_PATCH,
            'HIDDEN_PATCH_REMOVAL' => HIDDEN_PATCH_REMOVAL,
        ];
    }

    //
    // Get the correct url for the site
    //
    private function getUrls(Request $request)
    {
        // strip a lot to be extra safe against xss
        $url = $request->getRequestUri();
        $url = urldecode($url);
        $url = strip_tags($url);
        $url = htmlentities($url);
        $url = str_ireplace(' ', '+', $url);

        // if account, don't include.
        if ($url == '/account' || $url == '/login' || $url == '/register' || $url == '/forgot-password') {
            $url = null;
        }

        // set urls
        $data = [
            'en' => URL . $url,
            'ja' => URL_JA . $url,
            'fr' => URL_FR . $url,
            'de' => URL_DE . $url,
            'cns' => URL_CNS . $url,
            'cnt' => URL_CNT . $url,
        ];

        // current url
        $data['current'] = $data[LANGUAGE];
        $data['current'] = parse_url($data['current']);
        $data['current'] = sprintf('%s://%s', $data['current']['scheme'], $data['current']['host']);

        return $data;
    }

    //
    // Device checking
    //
    private function getDevices()
    {
        $detect = new \Mobile_Detect();

        return [
            'mobile' => $detect->isTablet() ? false : $detect->isMobile(),
            'tablet' => $detect->isTablet(),
        ];
    }
}
