<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppProfiles
//
trait AppProfiles
{
    protected function _profiles()
    {
        //
        // Show users profile
        //
        $this->route('/profile/{username}', 'GET', function(Request $request, $username)
        {
            $this->noApi($request);

            $user = $this->getModule('users')->getByUsername($username);
            if (!$user) {
                return $this->show404();
            }

            $user
                ->getCharacters()
                ->getGearsets()
                ->getComments()
                ->getScreenshots();

            return $this->respond('Profiles/Users/index.twig', (array)$user);
        });

        //
        // The ranking page
        //
        $rankingFunction = function(Request $request, $server = null)
        {
            $this->noApi($request);

            $dbs = $this->getModule('database');
            $dbs->QueryBuilder
                ->select([
                    'characters_ranking' => [
                        'updated as rank_updated',
                        'rank_global', 'rank_global_previous',
                        'rank_server', 'rank_server_previous',
                        'points as rank_points'
                    ],
                    'characters' => [
                        'lodestone_id', 'name', 'server', 'avatar', 'last_updated',
                    ]
                ])
                ->from('characters_ranking')
                ->join(['characters_ranking' => 'lodestone_id'], ['characters' => 'lodestone_id'])
                ->limit(0,100);

            if ($server) {
                $dbs->QueryBuilder
                    ->where('characters_ranking.rank_server > 0')
                    ->where('characters_ranking.server = :server')
                    ->bind('server', $server)
                    ->order('characters_ranking.rank_server', 'asc');
            } else {
                $dbs->QueryBuilder
                    ->where('characters_ranking.rank_global > 0')
                    ->order('characters_ranking.rank_global', 'asc');
            }

            $ranking = $dbs->get()->all();

            // add url to all characters
            foreach($ranking as $i => $character) {
                $url = sprintf('/character/%s/%s/%s',
                    $character['lodestone_id'],
                    strtolower(str_ireplace(' ', '+', $character['name'])),
                    strtolower($character['server'])
                );

                $ranking[$i]['url'] = $url;
            }

            $moment = $this->getModule('moment', '@'. strtotime('tomorrow'));
            $countdown = $moment->fromNow()->getRelative();
            $resettime = $moment->format('l jS F');

            return $this->respond('Profiles/Ranking/index.html.twig', [
                'ranking' => $ranking,
                'countdown' => $countdown,
                'resettime' => $resettime,
                'server' => $server,
            ]);
        };

        $this->route('/character/ranking', 'GET', $rankingFunction);
        $this->route('/character/ranking/{server}', 'GET', $rankingFunction);

        $this->route('/character/add', 'GET', function(Request $request)
        {
            if ($this->isApiRequest($request)) {
                if ($id = $request->get('id')) {
                    $xivsync = $this->getModule('xivsync');
                    $res = $xivsync->addCharacter($request->get('id'));
                    return $this->json($res);
                }

                return $this->error('Please provide a character lodestone id as the id parameter.');
            }

            $res = [];
            if ($id = $request->get('id')) {
                $xivsync = $this->getModule('xivsync');
                $res = $xivsync->addCharacter($request->get('id'));
            }

            return $this->respond('Profiles/Add/add.html.twig', [
                'res' => $res,
            ]);
        });

        $this->route('/character/privacy', 'GET', function(Request $request)
        {
            $message = false;

            $user = $this->getUser();
            $dbs = $this->getDatabase();


            if ($user && $request->get('enable') === '1') {

                $characters = array_keys($user->characters);
                if (!$characters) {
                    die('no');
                }

                $dbs->QueryBuilder
                    ->insert('characters_privacy')
                    ->schema(['id', 'hide_profile'])
                    ->duplicate(['hide_profile']);

                foreach($characters as $id) {
                    $dbs->QueryBuilder->values([$id, '1']);
                }

                $dbs->execute();
                $message = 'Characters have been made private.';

            } else if ($user && $request->get('enable') === '2') {

                $characters = array_keys($user->characters);
                if (!$characters) {
                    die('no');
                }

                foreach($characters as $id) {
                     $dbs->QueryBuilder
                        ->delete('characters_privacy')
                        ->where('id = :id')
                        ->bind('id', $id);

                    $dbs->execute();
                }

                $dbs->execute();
                $message = 'Characters are no longer private';

            }

            return $this->respond('Profiles/Privacy/index.html.twig', [
                'message' => $message,
            ]);
        });

        //
        // Show character profile
        //
        $content = function(Request $request, $id, $server = null, $name = null)
        {
            $name = str_ireplace('-', ' ', $name);
            $characters = $this->getModule('characters');
            $isApiRequest = $this->isApiRequest($request);

            // if server is numeric, its a lodestone id
            if (!is_numeric($id)) {
                return $this->show404($isApiRequest);
            }

            // process character data
            if ($characters->process($id)) {
                //
                // $chardata can be sent to the API, its mostly public
                // $tempdata is a lot of xivdb specific stuff
                //
                $chardata = $characters->getCharData();
                $tempdata = $characters->getTempData();

                $user = $this->getUser();

                $tempdata['member']['is_owner'] = false;
                if ($user && isset($tempdata['member'])) {
                    $tempdata['member']['is_owner'] = ($user->id === $tempdata['member']['id']);
                }

                // Add to API
                if ($isApiRequest) {
                    $data = $chardata;
                    $data['extras'] = [
                        'mounts' => [
                            'obtained' => $tempdata['mountsObtained'],
                            'total' => $tempdata['mountsTotal'],
                            'percent' => round($tempdata['mountsObtained'] / $tempdata['mountsTotal'], 3) * 100,
                        ],
                        'minions' => [
                            'obtained' => $tempdata['minionsObtained'],
                            'total' => $tempdata['minionsTotal'],
                            'percent' => round($tempdata['minionsObtained'] / $tempdata['minionsTotal'], 3) * 100,
                        ],
                    ];

                    // switch if data type passed
                    switch($request->get('data'))
                    {
                        default: break;
                        case 'events': $data = $tempdata['events']; break;
                        case 'tracking': $data = $tempdata['tracking']; break;
                        case 'gearsets': $data = $tempdata['gearsets']; break;
                        case 'achievements': $data = $tempdata['achievements']; break;
                        case 'achievements_possible': $data = $tempdata['achievements_possible']; break;
                        case 'achievements_obtained': $data = $tempdata['achievements_obtained']; break;
                        case 'minions_and_mounts_obtained': $data = $tempdata['minions_and_mounts']; break;
                        case 'custom': 

                        	$columns = explode(',', $request->get('columns'));
                        	$newData = [];

                        	if (empty($request->get('columns'))) {
                        		die('No provided columns query parameter');
                        	}

                        	foreach($columns as $col) {
                        		$newData[$col] = $data[$col] ?? null;
                        	}

                        	$data = $newData;

                        	break;
                    }

                    return $this->json($data);
                }

                return $this->respond('Profiles/Characters/index.html.twig', array_merge(
                    $chardata,
                    $tempdata
                ));
            }

            if ($isApiRequest && $characters->blacklisted) {
                return $this->json([
                    'code' => 200,
                    'error' => 'This character is private'
                ]);
            }

            if ($isApiRequest) {
                return $this->show404Characters($request);
            }

            if ($characters->blacklisted) {
                return $this->respond('Profiles/Characters/blacklisted.html.twig');
            }

            return $this->respond('Profiles/Characters/notfound.html.twig');
        };

        $this->route('/character/{id}', 'GET|POST|OPTIONS', $content);
        $this->route('/character/{id}/{name}', 'GET|POST|OPTIONS', $content);
        $this->route('/character/{id}/{name}/{server}', 'GET|POST|OPTIONS', $content);

        //
        // Hide a gearset
        //
        $this->route('/character/gearsets/{id}/{gs}/remove', 'GET', function(Request $request, $id, $gs)
        {
            $this->noApi($request);

            if (!$user = $this->getUser()) {
                return $this->json([false, 'User is not logged in.']);
            }

            $gs = explode('-', $gs);

            // check user owns character
            if (!$user->characters[$id]) {
                return $this->json([false, 'You do not own this gs.']);
            }

            $dbs = $this->getModule('database');
            $dbs->QueryBuilder
                ->delete('characters_gearsets')
                ->where('lodestone_id = :lodestoneId')->bind('lodestoneId', $id)
                ->where('classjob_id = :classjobId')->bind('classjobId', $gs[0])
                ->where('level = :level')->bind('level', $gs[1]);

            $dbs->execute();

            return $this->json([true, 'Gearset removed! Refresh the page when you are done.']);
        });

        //
        // The about page
        //
        $this->route('/xivsync', 'GET', function(Request $request)
        {
            $this->noApi($request);
            return $this->respond('Tools/XIVSync/index.twig');
        });
    }
}
