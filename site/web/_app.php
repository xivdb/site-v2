<?php
//
// XIVDB Frontend
//

// Blacklist check
$file = '_app';
require_once __DIR__ .'/_blacklist.php';

// set global timezone
date_default_timezone_set('UTC');

require_once __DIR__ .'/../app/defines.php';
require_once FILE_COMPOSER;

// site monitoring
if (SITE_MONITOR) {
	(new \XIVDB\Apps\Site\Tracking())->start('init');
}

// initialize default language
(new \XIVDB\Apps\Site\Language())->setDefaultLanguage();

// Initialize maintenance check
(new \XIVDB\Apps\Site\Maintenance());

// Start App
$AppKernal = new \XIVDB\Routes\AppFrontend\App();
$AppKernal->run();
