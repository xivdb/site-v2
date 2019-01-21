<?php

/**
 * @version 1.0
 * @author Josh Freeman <josh.freeman@rckt.co.uk>
 */

// composer auto loader
require_once __DIR__.'/vendor/autoload.php';

// start
return (new \XIVDB\Routes\AppFrontend\App())->get();
