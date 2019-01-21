<?php

namespace XIVDB\Routes\AppDashboard;

use Symfony\Component\HttpFoundation\Request;

//
// Home
//
trait AppMapper
{
    protected function _appMapper()
    {
		//
		// Home route
		//
        $this->route('/mapper', 'GET|POST', function(Request $request)
		{
			// permissions
            $this->mustBeModerator();

            $mapper = $this->getModule('mapper');
            $mapTotals = $mapper->getSubmissionTotals();
            $mapList = $mapper->getMapList();

            // file upload
            if ($file = $request->files->get('jsonfile')) {
                $json = file_get_contents($file->getRealPath());
                $mapper->uploadJson($json);
            }

            // claiming a map
            if ($request->get('action') == 'claim') {
                $mapper->claim($request->get('map'), $this->getUser());
            }

            $map = false; $claimed = false; $points = false;
            if ($mapid = $request->get('map')) {
                $map = $mapper->getMap($mapid);
                $claimed = $mapper->getClaimed($mapid);
                $points = $mapper->getPointsForMap($mapid);
            }

            return $this->respond('Dashboard/Mapper/index.html.twig', [
                'totals' => $mapTotals,
                'maplist' => $mapList,
                'points' => $points,
                'claimed' => $claimed,
                'map' => $map,
            ]);
        });

        //
        // Home route
        //
        $this->route('/mapper/delete-marker', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeModerator();

            $mapper = $this->getModule('mapper');
            $mapper->deletePoint($request->get('hash'));

            return $this->json(true);
        });
    }
}
