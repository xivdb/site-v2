<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;
use XIVDB\Apps\Content\Content;
use XIVDB\Apps\Content\ContentDB;
use \Moment\Moment;
use XIVDB\Apps\Site\RedisCache;

//
// AppContent
//
trait AppContent
{
    //
    // Content routes
    //
    public $types = [
        'item' => ContentDB::ITEMS,
        'achievement' => ContentDB::ACHIEVEMENTS,
        'action' => ContentDB::ACTIONS,
        'gathering' => ContentDB::GATHERING,
        'instance' => ContentDB::INSTANCES,
        'leve' => ContentDB::LEVES,
        'enemy' => ContentDB::ENEMY,
        'emote' => ContentDB::EMOTE,
        'placename' => ContentDB::PLACENAMES,
        'status' => ContentDB::STATUS,
        'title' => ContentDB::TITLES,
        'recipe' => ContentDB::RECIPE,
        'quest' => ContentDB::QUEST,
        'shop' => ContentDB::SHOP,
        'npc' => ContentDB::NPC,
        'minion' => ContentDB::MINIONS,
        'mount' => ContentDB::MOUNTS,
        'weather' => ContentDB::WEATHER,
        'fate' => ContentDB::FATES,
        'special-shop' => ContentDB::SPECIAL_SHOPS,
    ];

    // list of game data accessible.
    private $gamedata = [
        'servers'       => [ 'Server list', 'xiv_worlds_servers'],
        'exp_table'     => [ 'obtain the experience points table', 'xiv_param_grow', '*', 'exp > 0'],
        'classjobs'     => [ 'obtain a list of class/jobs', 'xiv_classjobs'],
        'cabinet'       => [ 'obtain a list of item ids that can go into the cabinet', 'xiv_cabinet' ],
        'armoire'       => [ '(alias) cabinet', 'xiv_cabinet' ],
        'gc'            => [ 'obtain a list of grand companies', 'xiv_gc' ],
        'patchlist'     => [ 'obtain a list of patches, controlled by xivdb', 'db_patch' ],
        'baseparams'    => [ 'Obtain a list of base parameters', 'xiv_baseparams' ],
        'towns'			=> [ 'Obtain a list of towns', 'xiv_towns' ],
        'guardians'  	=> [ 'Obtain a list of guardians', 'xiv_guardian' ],
        'journal_category' => [ 'Obtain a list of journal categories', 'xiv_journal_category'],
        'journal_genre' => [ 'Obtain a list of journal genres', 'xiv_journal_genre'],
        'journal_sections' => [ 'Obtain a list of journal sections', 'xiv_journal_section'],
    ];

    //
    // Get the url type
    //
    public function getTypeFromRequest()
    {
        $type = $this->request->getPathInfo();
        $type = explode('/', $type);
        $type = array_filter($type);
        $type = array_values($type);
        $type = $type[0];
        $type = trim($type);
        $type = strtolower($type);
        return $type;
    }

    //
    // Site contents :D
    //
    protected function _content()
    {
        //
        // Core content, name not required (not used)
        //
        $content = function(Request $request, $id, $name = null)
        {
            $qty = $request->get('quantity');
            $redis = $this->getRedis();

            // set last url
            $this->request = $request;
            $this->setLastUrl($this->request);

            // get content type
            $type = $this->getTypeFromRequest();

            // get content id
            $id = is_numeric($id) ? filter_var(substr($id, 0, 32), FILTER_SANITIZE_NUMBER_INT) : $id;

            // get real id
            if ($type == 'item' && strlen($id) > 5) {
                $dbs = $this->getModule('database');
                $dbs->QueryBuilder
                    ->select(['id'], false)
                    ->from('xiv_items')
                    ->where('lodestone_id = :id')
                    ->bind(':id', $id)
                    ->limit(0,1);

                $realId = $dbs->get()->one();

                if ($realId) {
                    $id = $realId['id'];
                }
            }

            //grab class from content type
            $class = $this->getContentClass($type);
            $class->setQuantity($qty);

            // if no id, type or class return 404
            if (!$id || !$type || !$class) {
                return $this->show404($request);
            }

            // get content
            $key = 'core_content_'.$type.'_'.$id;
            if (!$content = $redis->get($key)) {
                $content = $class->setId($id)->get();
                $redis->set($key, $content, 9999);
            }

            if (!$content) {
                return $this->show404($request);
            }

            // append cid and type
            $content['_cid'] = $class->getContentIds($type);
            $content['_type'] = $type;

            $user = $this->getUser();
            if ((!$user || !$user->isModerator()) && HIDDEN_PATCH && time() < HIDDEN_PATCH_REMOVAL) {
                if ($content['patch']['patch'] == HIDDEN_PATCH) {
                    // Add to API
                    if ($this->isApiRequest($request)) {
                        return $this->json([
                            'error' => 'This content is currently under restriction.'
                        ]);
                    }

                    return $this->respond('Content/restricted.twig');
                }
            }

            // Add to API
            if ($this->isApiRequest($request)) {
                return $this->json($content);
            }

            // add to history
            $this->addToHistory($content);

            // get screenshots class
            $screenshots = $this->getModule('screenshots');
            $totalScreenshots = $screenshots->getTotal($content['id'], $content['_cid']);
            $bannerScreenshot = $screenshots->getContentBanner($content['id'], $content['_cid']);

            // get comments class
            $comments = $this->getModule('comments');
            $totalComments = $comments->getTotal($content['id'], $content['_cid']);
            $linkedComments = $comments->getLinked($content['id'], $content['url_type']);

            // if linked comments, do some tweaks
            if ($linkedComments) {
                foreach($linkedComments as $i => $cmt) {
                    $linkedComments[$i]['content'] = $class->getContentToId($cmt['content'], $cmt['uniq']);
                    $linkedComments[$i]['text'] = $this->markdown($cmt['text']);
                    $linkedComments[$i]['time'] = (new Moment($cmt['time']))->fromNow()->getRelative();
                }
            }

            // latest comment
            $latestComment = $comments->getLatestComment($content['id'], $content['_cid']);
            $latestScreenshot = $screenshots->getLatestScreenshot($content['id'], $content['_cid']);

            // respond, also include maps
            return $this->respond('Content/index.twig', [
                'content' => $content,
                'bannerScreenshot' => $bannerScreenshot,
                'totalScreenshots' => $totalScreenshots,
                'latestScreenshot' => $latestScreenshot,
                'totalComments' => $totalComments,
                'latestComment' => $latestComment,
                'linkedComments' => $linkedComments,
            ]);
        };

        //
        // List content
        //
        $list = function(Request $request)
        {
            $this->request = $request;
            $type = $this->getUrlType();

            // modules
            $redis = $this->getRedis();
            $dbs = $this->getModule('database');

            // default columns to use
            $default = [
                'id',
                'name_{lang} as name',
                'name_ja',
                'name_en',
                'name_fr',
                'name_de',
                'name_cns',
                'lodestone_id',
                'lodestone_type'
            ];

            // accepted columns
            $class = $this->getContentClass($type);
            $allowed = array_unique(array_merge($default, $class::$main), SORT_REGULAR);

            // if showing schema
            if ($schema = $request->get('schema')) {
                $data = $allowed;
            } else {
                // get columns
                $columns = $request->get('columns');
                $columns = $columns ? explode(',', $columns) : $default;
                asort($columns);

                // if using custom columns
                if ($request->get('columns')) {
                    foreach($columns as $i => $col) {
                        if ($col == 'name') {
                            $col = 'name_{lang} as name';
                            $columns[$i] = $col;
                        }

                        if ($col != 'name' && !in_array($col, $allowed)) {
                            return $this->json(['error' => 'Invalid column: '. $col]);
                        }
                    }
                }

                $columnHash = sha1(json_encode($columns));
                $key = 'api_list_'. $type .'_'. $columnHash;
            }

            // if not cached, get again
            if (!$schema && !$data = $redis->get($key)) {
                $table = isset($this->types[$type]) ? $this->types[$type] : null;

                // if table exists
                if ($table) {
                    // get data
                    $dbs->QueryBuilder
                        ->select($columns)
                        ->from($table)
                        ->order('id', 'asc');

                    if (HIDDEN_PATCH && time() < HIDDEN_PATCH_REMOVAL) {
                        $dbs->QueryBuilder->where('patch != '. HIDDEN_PATCH);
                    }

                    // get
                    $data = $dbs->get()->all();
                    $redis->set($key, $data, CACHE_GAME_CONTENT_LISTS);
                }
            }

            // Add to API
            if ($this->isApiRequest($request)) {
                return $this->json($data);
            }

            // return 404!
            return $this->show404($request);
        };

        //
        // Handle sitemap
        //
        $sitemap = function(Request $request)
        {
            $sitemap = 'Could not generate';
            $this->request = $request;

            // modules
            $redis = $this->getRedis();
            $dbs = $this->getModule('database');

            // get type
            $type = (!empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'item');
            $type = $this->v1Route($type);

            // cache key
            $key = 'sitemap_'. $type;

            // if not cached, get again
            if (!$sitemap = $redis->get($key))
            {
                $table = isset($this->types[$type]) ? $this->types[$type] : null;

                // if table exists
                if ($table)
                {
                    $dbs->QueryBuilder
                        ->select(['id', 'name_{lang} as name'])
                        ->from($table)
                        ->order('id', 'asc');

                    // get
                    $data = $dbs->get()->all();

                    // generate sitemap
                    $sitemap = [];
                    $sitemap[] = '<?xml version="1.0" encoding="UTF-8"?>';
                    $sitemap[] = '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
                    foreach($data as $i => $d) {
                        $modified = date('Y-m-d');
                        $id = $d['id'];

                        $urls = [
                            'en' => URL . $this->url($type, $id),
                            'fr' => URL_FR . $this->url($type, $id),
                            'ja' => URL_JA . $this->url($type, $id),
                            'de' => URL_DE . $this->url($type, $id),
                        ];

                        $url = $urls[LANGUAGE];

                        // XML Row
                        $sitemap[] = "  <url>";
                        $sitemap[] = "      <loc>{$url}</loc>";
                        $sitemap[] = "      <lastmod>{$modified}</lastmod>";
                        $sitemap[] = "      <changefreq>weekly</changefreq>";
                        $sitemap[] = "      <priority>1.0</priority>";
                        $sitemap[] = "  </url>";
                    }

                    $sitemap[] = '</urlset>';
                    $sitemap = implode("\n", $sitemap);
                    $redis->set($key, $sitemap, TIME_5MINUTES);
                }
            }

            echo $sitemap;
            die;
        };

        //
        // Attach the route
        //
        foreach(array_keys($this->types) as $route)
        {
            // Main content pages, accessible with and without name
            $this->route('/'. $route .'/{id}/{name}', 'GET|POST|OPTIONS', $content);
            $this->route('/'. $route .'/{id}/{name}/', 'GET|POST|OPTIONS', $content);
            $this->route('/'. $route .'/{id}', 'GET|POST|OPTIONS', $content);
            $this->route('/'. $route .'/{id}/', 'GET|POST|OPTIONS', $content);

            // full lists for the content type
            $this->route('/'. $route .'/', 'GET|POST|OPTIONS', $list);
            $this->route('/'. $route, 'GET|POST|OPTIONS', $list);

            // sitemaps
            $this->route('/sitemap/', 'GET|POST|OPTIONS', $sitemap);
            $this->route('/sitemap', 'GET|POST|OPTIONS', $sitemap);
        }

        //
        // Achievement census
        //
        $this->route('/census/achievements', 'GET|OPTIONS', function(Request $request)
        {
            $redis = $this->getRedis();
            $key = 'achievement_census';

            if (!$data = $redis->get($key)) {
                // get data
                $dbs = $this->getModule('database');
                $dbs->QueryBuilder
                    ->select('*')
                    ->from('stats_achievements')
                    ->order('achievement_id', 'asc');

                $data = $dbs->get()->all();
                $redis->set($key, $data, TIME_24HOUR);
            }

            // Add to API
            if ($this->isApiRequest($request)) {
                return $this->json($data);
            }

            // return 404!
            return $this->show404($request);
        });

        //
        // Achievement statistics (quite logic heavy, TODO: Move to some class)
        //
        $this->route('/achievement/{id}/xivsync/census', 'GET|OPTIONS', function(Request $request, $id)
        {
            if (!is_numeric($id)) {
                return $this->show404();
            }

            // enforce low int
            $id = substr($id, 0, 20);
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $redis = $this->getRedis();
            $key = 'achievement_census_'. $id;

            if ($data = $redis->get($key)) {
                return $this->json($data);
            }

            $dbs = $this->getModule('database');
            $dbs->QueryBuilder
                ->select('*')
                ->from('stats_achievements')
                ->where('achievement_id = :id')->bind('id', $id);

            $data = $dbs->get()->one();

            // set vars
            $response = [
                'obtained' => $data['total_obtained'],
                'eligable' => $data['total_possible'],
                'obtained_percent' => $data['total_obtained_percent'],
                'release_date' => $data['release_date'],
                'last_updated' => $data['last_updated'],
            ];

            $redis->set($key, $response, TIME_24HOUR);

            return $this->json($response);
        });

        //
        // Specific for lodestone api
        //
        $this->route('/lodestone/php/items', 'GET|OPTIONS', function(Request $request)
        {
            /** @var RedisCache $redis */
            $redis = $this->getRedis();

            if (!$items = $redis->get('lodestone_php_items')) {
                $searchCategories = [
                    // weapons
                    1,2,3,4,5,6,7,8,9,10,84,87,
                    88,89,

                    // shield
                    11,

                    // tools
                    12,13,14,15,16,17,18,19,20,
                    21,22,23,24,25,26,27,28,29,
                    30,31,32,

                    // gear
                    34,35,36,37,38,39,40,41,42,
                    43,

                    // dye, materia, soul crystal
                    55,58,62,
                ];

                $dbs = $this->getModule('database');
                $dbs->QueryBuilder
                    ->select(['id','name_{lang} as name'])
                    ->from('xiv_items')
                    ->where('id > 1600')
                    ->where('item_ui_category IN ('. implode(',', $searchCategories) .')')
                    ->order('id', 'asc');

                $items = $dbs->get()->all();
                $redis->set('lodestone_php_items', $items, TIME_1WEEK);
            }

            return $this->json($items);
        });
    }

    //
    // Access to game data lists
    //
    protected function _gamedata()
    {
        $this->route('/data', 'GET|OPTIONS', function(Request $request)
        {
            $array = [];
            foreach($this->gamedata as $i => $row) {
                $array[] = [
                    'type' => $i,
                    'description' => $row[0]
                ];
            }

            if ($this->isApiRequest($request)) {
                return $this->json($array);
            }

            return $this->show404($this->isApiRequest($request));
        });

        $this->route('/data/{type}', 'GET|OPTIONS', function(Request $request, $type)
        {
            $gd = $this->getModule('gamedata');

            // check if type exists
            if (isset($this->gamedata[$type])) {
                $data = $this->gamedata[$type];

                // set fields
                $table = $data[1];
                $columns = isset($data[2]) ? $data[2] : '*';
                $where = isset($data[3]) ? $data[3] : false;

                if ($this->isApiRequest($request)) {
                    return $this->json($gd->xivGameDataBasic($table, $columns, $where));
                }
            }

            switch($type)
            {
                case 'cabinet':
                case 'armoire':
                    $data = (new \XIVDB\Apps\Content\DataArmoire())->get();
                    return $this->respond('Pages\Data\armoire.html.twig', $data); break;
            }

            return $this->show404($this->isApiRequest($request));
        });
    }
}
