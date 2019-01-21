<?php
// site version
define('VERSION', 'v401stylefixes');

// Languages:
//  ja (japanese)
//  en (english)
//  fr (french)
//  de (german)
//  cns (chinese simplified)
//  cnt (chinese traditional)
define('DEFAULT_LANGUAGE', 'en');

//
// Import all settings
//
require __DIR__ .'/settings.paths.php';
require __DIR__ .'/settings.times.php';
require __DIR__ .'/settings.patches.php';
require __DIR__ .'/settings.cache.php';
require __DIR__ .'/settings.urls.php';
require __DIR__ .'/settings.meta.php';
require __DIR__ .'/settings.misc.php';
require __DIR__ .'/settings.recaptcha.php';
require __DIR__ .'/settings.search.php';
require __DIR__ .'/settings.cookies.php';
require __DIR__ .'/settings.mail.php';
require __DIR__ .'/settings.extract.php';

// --------------------------------------------------------
// Dirty show function, makes print_r pretty
// --------------------------------------------------------

function show($data, $vars = [], $noPre = false)
{
    if (!SHOW) return;

    if ($vars) $data = vsprintf($data, $vars);

    if ($noPre) {
        echo print_r($data, true);
        return;
    }

    echo '<pre>'. print_r($data, true) .'</pre>';
}
