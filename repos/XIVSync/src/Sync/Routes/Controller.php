<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
	use FrontendController;
	use CharacterController;
	use LodestoneController;
	use FreeCompanyController;
	use LinkshellController;
	use DefaultController;

	protected $Silex;

	function __construct()
	{
		$this->Silex = new \Silex\Application();
		$this->SilexSetup();
		$this->Silex->boot();

		// Initialize routes
        $methods = get_class_methods($this);

        // go through methods and call this classes methods
        foreach($methods as $route) {
            if ($route[0] == '_' && $route[1] != '_') {
                // run route
                $this->$route();
            }
        }

		$this->Silex->run();
	}

	//
	// Setup silex
	//
	protected function SilexSetup()
	{
		$config = require CONFIG;

		// Set debug mode
		$this->Silex['debug'] = DEV;

		// Register twig provider
        $this->Silex->register(new \Silex\Provider\TwigServiceProvider(), [
            'twig.path' => TEMPLATES,
            'twig.options' => [
                'debug' => DEV,
                'cache' => false,
            ],
        ]);

		$this->Silex->register(new \Silex\Provider\DoctrineServiceProvider(), array(
		    'dbs.options' => [
		        'xivsync' => [
		            'driver'    => 'pdo_mysql',
		            'host'      => $config['database']['host'],
		            'dbname'    => $config['database']['name'],
		            'user'      => $config['database']['user'],
		            'password'  => $config['database']['pass'],
		            'charset'   => 'utf8',
		        ],
		    ],
		));

		// attach before listener
        $this->Silex->before(function (Request $request)
        {
			$this->Silex['twig']->addGlobal('global', [
				'MAINTENANCE' => MAINTENANCE,
			]);
		});

		$this->Silex->after(function(Request $request, Response $response)
		{
		    if ($response instanceof JsonResponse) {
		        $response->setEncodingOptions(JSON_PRETTY_PRINT);
		    }

		    return $response;
		});
	}

	//
	// Access database
	//
	protected function Database()
	{
		return $this->Silex['db'];
	}

	//
    // Attach a route onto silex
    //
    protected function route($path, $methods, $function)
    {
        return $this->Silex->match($path, $function)->method($methods);
    }

    //
    // Respond with a twig template injected with some data
    //
    protected function respond($template, $data = [])
    {
        return $this->Silex['twig']->render($template, $data);
    }

    //
    // Redirect to another url
    //
    protected function redirect($url)
    {
        return $this->Silex->redirect($url);
    }

    //
    // Respond with json
    //
    protected function json($data)
    {
        return $this->Silex->json($data, 200, [
			'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
		]);
    }

    //
    // Lock a controller route
    //
    protected function locked($request)
    {
        // so secure!
        if ($request->get('key') != API_KEY) {
            die;
        }
    }
}
