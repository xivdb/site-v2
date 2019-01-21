<?php
// Load stuff
define('OUTPUT', false);
require __DIR__ .'/../defines.php';
require __DIR__ .'/../src/misc.php';
require __DIR__ .'/../vendor/autoload.php';
require __DIR__ .'/../maintenance.php';

// Preload XIVDB Data
require __DIR__ .'/../src/xivdb_preload.php';

// start
(new Sync\Routes\Controller());
