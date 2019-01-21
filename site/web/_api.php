<?php
//
// XIVDB APi
//

// Blacklist check
$file = '_api';
require_once __DIR__ .'/_blacklist.php';

// set global timezone
date_default_timezone_set('UTC');

require_once __DIR__ .'/../app/defines.php';
require_once FILE_COMPOSER;

// validate request is an API one
$address = explode('.', strtolower($_SERVER['SERVER_NAME']));
if ($address[0] != 'api') {
	die('Invalid request');
}

// site monitoring
if (SITE_MONITOR) {
	(new \XIVDB\Apps\Site\Tracking())->start('init');
}

// initialize default language
(new \XIVDB\Apps\Site\Language())->setDefaultLanguage();

// Initialize maintenance check
(new \XIVDB\Apps\Site\Maintenance(true));

// Start App
$AppKernal = new \XIVDB\Routes\AppFrontend\App();
$AppKernal->run();
