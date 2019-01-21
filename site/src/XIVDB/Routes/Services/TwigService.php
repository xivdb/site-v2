<?php

namespace XIVDB\Routes\Services;

use Silex\Provider\TwigServiceProvider;

//
// TwigService
//
trait TwigService
{
    protected function registerTwigService()
    {
        // Register twig provider
        $this->SilexApplication->register(new TwigServiceProvider(), [
            'twig.path' => ROOT_VIEWS,
            'twig.options' => [
                'debug' => DEV,
                'cache' => ROOT_TWIGCACHE,
            ],
        ]);
    }
}
