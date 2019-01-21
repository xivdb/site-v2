<?php
//
// Times
//
define('TIME_60SECONDS',    60);
define('TIME_5MINUTES',     TIME_60SECONDS * 5);
define('TIME_15MINUTES',    TIME_60SECONDS * 15);
define('TIME_30MINUTES',    TIME_60SECONDS * 30);
define('TIME_60MINUTES',    TIME_60SECONDS * 60);
define('TIME_3HOURS',    	TIME_60MINUTES * 3);
define('TIME_6HOURS',       TIME_60MINUTES * 6);
define('TIME_12HOURS',      TIME_60MINUTES * 12);
define('TIME_24HOUR',       TIME_60MINUTES * 24);
define('TIME_1WEEK',        TIME_24HOUR * 7);
define('TIME_30DAYS',       TIME_24HOUR * 30);
define('TIME_90DAYS',       TIME_24HOUR * 90);
define('TIME_180DAYS',      TIME_24HOUR * 180);
define('TIME_365DAYS',      TIME_24HOUR * 365);

// formats
define('DATE_MYSQL', 'Y-m-d H:i:s');
define('DATE_FULL', 'F j, Y, g:i a');

// now
define('TIME', time());
define('TIME_MS', round(microtime(true) * 1000));

// timezone
define('TIMEZONE', 'UTC');
