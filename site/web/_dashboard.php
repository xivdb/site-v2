<?php
//
// XIVDB dashboard
//

// set global timezone
date_default_timezone_set('UTC');

// Need defines, composer and config
require_once __DIR__ .'/../app/defines.php';
require_once FILE_COMPOSER;

// initialize default language
(new \XIVDB\Apps\Site\Language())->setDefaultLanguage();

// Start App
$AppKernal = new \XIVDB\Routes\AppDashboard\App();
$AppKernal->run();
