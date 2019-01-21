<?php

// composer auto loader
require_once __DIR__.'/vendor/autoload.php';

// start
return (new \XIVDB\Routes\AppFrontend\App())->get();
