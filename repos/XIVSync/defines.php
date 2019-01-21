<?php
// site config
define('ROOT', __DIR__);
define('DEV', true);

// allow output if not web
if (!defined('OUTPUT')) {
    define('OUTPUT', true);
}

// So secure!! ...
define('API_KEY', 'xivsync');

// Cache directory
define('XIVDB_FILE', __DIR__.'/src/xivdb.php');
define('XIVDB_CACHE_DURATION', (60 * 60 * 24 * 30));

// Templates
define('TEMPLATES', __DIR__.'/src/Sync/Views');

// Stats
define('STATS_JSON', __DIR__.'/src/Sync/Views/Home/stats.json');

// Path to config file
define('CONFIG', __DIR__ .'/config.php');

// Current timestamp
define('START_TIME', time());

// How many seconds until the execution of updates cancels, this is
// to prevent overflow of characters being updated when the cronjob
// will run again. Set it less than the cronjob time.
define('TIME_LIMIT', 58);

// Maximum amount of characters to update per cronjob stream
define('AUTO_UPDATE_MAX', 100);

// If enabled, last active time restrictions are in place for auto-update
define('AUTO_UPDATE_TIME_RESTRICTED', false);

// If a new hash should be generated
define('AUTO_UPDATE_SET_LAST_ACTIVE', true);

// Maximum amount of characters to add per cronjob stream
define('AUTO_ADD_MAX', 50);

// Maximum amount of achievements to add per cronjob stream
define('AUTO_ACHIEVEMENT_MAX', 10);

// Maximum amount of free companies to add per cronjob stream
define('AUTO_UPDATE_FC_MAX', 25);

// Base url for parsing characters
define('LODESTONE_URL', 'http://na.finalfantasyxiv.com/');
define('LODESTONE_CHARACTERS_URL', LODESTONE_URL . 'lodestone/character/%s/');
define('LODESTONE_CHARACTERS_FRIENDS_URL', LODESTONE_URL . 'lodestone/character/%s/friend');
define('LODESTONE_CHARACTERS_FOLLOWING_URL', LODESTONE_URL . 'lodestone/character/%s/following');
define('LODESTONE_CHARACTERS_SEARCH_URL', LODESTONE_URL .'lodestone/character');
define('LODESTONE_ACHIEVEMENTS_URL', LODESTONE_URL . 'lodestone/character/%s/achievement/kind/%s/');
define('LODESTONE_FREECOMPANY_URL', LODESTONE_URL . 'lodestone/freecompany/%s/');
define('LODESTONE_FREECOMPANY_SEARCH_URL', LODESTONE_URL . 'lodestone/freecompany');
define('LODESTONE_FREECOMPANY_MEMBERS_URL', LODESTONE_URL .'lodestone/freecompany/%s/member/');
define('LODESTONE_LINKSHELL_SEARCH_URL', LODESTONE_URL . 'lodestone/linkshell');
define('LODESTONE_LINKSHELL_MEMBERS_URL', LODESTONE_URL .'lodestone/linkshell/%s/');

define('LODESTONE_BANNERS', LODESTONE_URL .'lodestone/');
define('LODESTONE_NEWS', LODESTONE_URL .'lodestone/news/');
define('LODESTONE_TOPICS', LODESTONE_URL .'lodestone/topics/');
define('LODESTONE_NOTICES', LODESTONE_URL .'lodestone/news/category/1');
define('LODESTONE_MAINTENANCE', LODESTONE_URL .'lodestone/news/category/2');
define('LODESTONE_UPDATES', LODESTONE_URL .'lodestone/news/category/3');
define('LODESTONE_STATUS', LODESTONE_URL .'lodestone/news/category/4');
define('LODESTONE_FEAST_SEASON_1', LODESTONE_URL .'lodestone/ranking/thefeast/result/1/');
define('LODESTONE_FEAST_SEASON_2', LODESTONE_URL .'lodestone/ranking/thefeast/result/2/');
define('LODESTONE_FEAST_SEASON_3', LODESTONE_URL .'lodestone/ranking/thefeast/result/3/');
define('LODESTONE_FEAST_SEASON_4', LODESTONE_URL .'lodestone/ranking/thefeast/'); // CURRENT
define('LODESTONE_DEEP_DUNGEON', LODESTONE_URL .'lodestone/ranking/deepdungeon/');
define('LODESTONE_WORLD_STATUS', LODESTONE_URL .'lodestone/worldstatus/');
define('LODESTONE_DEV_BLOG', LODESTONE_URL .'/pr/blog/atom.xml');
define('LODESTONE_FORUMS', 'http://forum.square-enix.com/ffxiv/');
define('LODESTONE_FORUMS_HOMEPAGE', LODESTONE_FORUMS .'forum.php');

// Minimum amount of exp to allow an exp event
define('MINIMUM_EXP_FOR_EVENT', 1000);

// Query output truncate
define('SQL_OUTPUT_TRUNCATE', 30);

// times
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
