<?php
//
// XIVDB Secure areas
//

// set global timezone
date_default_timezone_set('UTC');

require_once __DIR__ .'/../app/defines.php';
require_once FILE_COMPOSER;

// validate request is an API one
$address = explode('.', strtolower($_SERVER['SERVER_NAME']));
if ($address[1] != 'secure') {
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
$AppKernal = new \XIVDB\Routes\AppSecure\App();
$AppKernal->run();
