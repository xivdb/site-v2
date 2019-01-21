<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

use XIVDB\Apps\Tools\Maps\Maps;

//
// AppTools
//
trait AppToolMaps
{
    protected function _maps()
    {
        /**
         * Get a full list of maps
         */
        $this->route('/maps', 'GET|OPTIONS', function(Request $request)
        {
            $this->setLastUrl($request);

            // maps class
            $maps = new Maps();
            $list = $maps->getList();
            $layers = $maps->getLayers();
            $placenames = $maps->getPlacenames();

            // if debugging the page
            if ($request->get('debug')) {
                exit(show($list));
            }

            // if this is a json request
            if ($this->isApiRequest($request)) {
                if ($request->get('pretty')) {
                    exit(show(json_encode($list, JSON_PRETTY_PRINT)));
                }

                return $this->json($list);
            }

            // new game list
            return $this->respond('Tools/Maps/index.twig', [
                'list' => $list,
                'layers' => $layers,
                'placenames' => $placenames,
            ]);
        });

        $this->route('/maps/nodes', 'GET|OPTIONS', function(Request $request)
    	{
    		$dbs = $this->getModule('database');
    		$redis = $this->getRedis();

    		if ($data = $redis->get('map_node_gathering')) {
    			return $this->json($data);
    		}

            // get dev blog posts
            $dbs->QueryBuilder
                ->select('*', false)
                ->from('app_mapper')
                ->where("content_type = 'Gathering'")
                ->order('id', 'desc');

            $data = $dbs->get()->all();

            foreach($data as $i => $row) {
            	$data[$i]['data'] = json_decode($row['data']);
            	$data[$i]['position'] = json_decode($row['position']);
            }

            $redis->set('map_node_gathering', $data, (TIME_60MINUTES * 300));

            return $this->json($data);
    	});

        /**
         * Get map layers for a specific flag type,
         * - flags: 'id', 'region', 'placename', 'zone', 'territory_id'
         * - Provides: each layer for a type, placename, region, zone, image, layer count, size/offset.
         */
        $this->route('/maps/get/layers/{type}', 'GET|OPTIONS', function(Request $request, $type)
        {
            // if not an api request, end
            if (!$this->isApiRequest($request)) {
                return $this->show404();
            }

            // accepted types
            $acceptedParameters = ['id', 'region', 'placename', 'zone', 'territory_id'];

            // only accept specific types
            if (!in_array($type, $acceptedParameters)) {
                return $this->json([
                    'status' => false,
                    'message' => 'Type parameter was invalid, accepts: '. implode(',', $acceptedParameters),
                ]);
            }

            // get layers and return list
            $data = $this->getModule('maps')->getLayersForType($type, $request->get('id'));
            $data = [
                'status' => true,
                'data' => $request->get('id') && isset($data[$request->get('id')]) ? $data[$request->get('id')] : $data,
            ];

            return $this->json($data);
        });

        /**
         * Get markers for a map
         */
        $this->route('/maps/get/markers', 'GET|OPTIONS', function(Request $request)
        {
            $maps = $this->getModule('maps');

            if ($placenameId = $request->get('placename')) {
                $markers = $maps->getMarkersForPlacename($placenameId);
            } else if ($mapId = $request->get('map')) {
                $markers = $maps->getMarkersForMap($mapId);
            }

            return $this->json($maps->handleMarkers($markers));
        });
    }
}
