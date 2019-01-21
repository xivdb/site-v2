<?php

namespace XIVDB\Routes\Services;

use Knp\Provider\ConsoleServiceProvider;

//
// ConsoleService
//
trait ConsoleService
{
    protected function registerConsoleService()
    {
        $this->SilexApplication->register(new ConsoleServiceProvider(), [
            'console.name' => 'XIVDB',
            'console.version' => '1.0.0',
            'console.project_directory' => __DIR__ . '/..'
        ]);
    }
}
