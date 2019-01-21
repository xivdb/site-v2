<?php
//
// Misc stuff
//

// hidden patch
define('HIDDEN_PATCH', false);//36); //36);
define('HIDDEN_PATCH_REMOVAL', 1497916800);

// Include javascript monitor
define('SITE_MONITOR', false);
define('SITE_MONITOR_FILE', ROOT .'/log.txt');

// The page route for error 404
define('ERROR_404', '/are-you-lost');

// Bcrypt security value
define('BCRYPT_COST', 12);

// The maximum amount of tooltip ids allowed
define('MAX_TOOLTIP_IDS', 250);

// The maximum number of history items
define('MAX_HISTORY_ITEMS', 15);

// Allow the manager to insert into the database
define('MANAGER_INSERT', true);

// Maximum amount of inserts the manager can do per query
define('MANAGER_INSERT_LIMIT', 50);

// Track stuff (debugging purposes)
define('PAGE_HIT_TRACKING', false);
define('REDIS_HIT_TRACKING', false);

// Max level
define('GAME_MAX_LEVEL', 70);
