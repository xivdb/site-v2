<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppToolGearsets
//
trait AppToolGearsets
{
	protected function _gearsets()
    {
		//
		// Shopping cart showcase/about page
		//
		$this->route('/gearsets', 'GET', function(Request $request)
		{
		    $this->noApi($request);
			$this->setLastUrl($request);

			return $this->redirect('/devblog?id=12');
			#return $this->respond('Tools/Gearsets/index.html.twig');
		});

		//
		// Save a gearset
		//
		$this->route('/gearsets/save', 'GET|POST', function(Request $request)
		{
			$this->noApi($request);
			if (!$this->getUser()) {
				return $this->json(false);
			}

			// Save a gearset
			$json = $request->get('json');
			$name = trim($request->get('name'));
			$desc = trim($request->get('desc'));
			$type = trim($request->get('type'));
			$classjob = trim($request->get('classjob'));

			if ($id = $request->get('id')) {
				$this->getModule('gearsets')->update($id, $name, $desc, $type, $classjob, $json);
				return $this->json('updated');
			}

			$this->getModule('gearsets')->save($name, $desc, $type, $classjob, $json);
			return $this->json('created');
		});

		//
		// Load a gearset
		//
		$this->route('/gearsets/load', 'GET|OPTIONS', function(Request $request)
		{
			$this->noApi($request);
			if (!$this->getUser()) {
				return $this->json(false);
			}

			$gearset = $this->getModule('gearsets')->load();

			return $this->json($gearset);
		});
	}
}
