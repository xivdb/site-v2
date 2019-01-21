<?php

namespace XIVDB\Routes;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Silex\Application;

//
// Handle stuff related to Routes
//
class RouteHandler extends \XIVDB\Apps\AppHandler
{
    use \XIVDB\Routes\Services\ConsoleService,
        \XIVDB\Routes\Services\TwigService,
        \XIVDB\Routes\Services\UrlService,
        \XIVDB\Routes\Services\SessionService,
        \XIVDB\Routes\Listeners\EventListener,
        \XIVDB\Routes\Listeners\ErrorListener,
        \XIVDB\Routes\Modules\ResponseModule,
        \XIVDB\Routes\Modules\APIModule,
        \XIVDB\Routes\Modules\UserModule,
        \XIVDB\Routes\Modules\PermissionsModule;

    // Main silex application
    public $SilexApplication;
    protected $Database;

    function __construct()
    {
        // Initialize Silex
        $this->initialize();

        // Initialize routes
        $methods = get_class_methods($this);

        // go through methods and call this classes methods
        foreach($methods as $route) {
            if ($route[0] == '_' && $route[1] != '_') {
                // run route
                $this->$route();
            }
        }
    }

    //
    // Run Silex
    //
    public function run()
    {
        $this->SilexApplication->run();
    }

    //
    // Either get silex or get a service from silex
    //
    public function get($service = null)
    {
        // services
        if ($service) {
            return $this->SilexApplication[$service];
        }

        return $this->SilexApplication;
    }

    //
    // Attach a route onto silex
    //
    protected function route($path, $methods, $function)
    {
        $this->SilexApplication
            ->match($path, $function)
            ->method($methods);
    }

    //
    // Initialize silex application
    //
    protected function initialize()
    {
        $this->SilexApplication = new Application();
        $this->SilexApplication['debug'] = DEV;

        // register services
        $this->registerConsoleService();
        $this->registerTwigService();
        $this->registerUrlService();
        $this->registerSessionService();

        // register triggers
        $this->registerEventListeners();
        $this->registerErrorListener();

        // boot
        $this->SilexApplication->boot();
    }

    //
    // Add a global twig variable
    //
    protected function addGlobal($index, $data)
    {
        $this->SilexApplication['twig']->addGlobal($index, $data);
    }

    //
    // Add a filter to twig
    //
    protected function addFilter($filter)
    {
        $this->SilexApplication['twig']->addFilter($filter);
    }

    //
    // Access to flashbag
    //
    protected function flashbag($key, $message)
    {
        $this->SilexApplication['session']->getFlashBag()->add($key, $message);
    }
}
