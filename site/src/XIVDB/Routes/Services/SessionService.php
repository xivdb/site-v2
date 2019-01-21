<?php

namespace XIVDB\Routes\Services;

//
// SessionService
//
trait SessionService
{
    protected function registerSessionService()
    {
        $this->SilexApplication->register(new \Silex\Provider\SessionServiceProvider());
    }
}
