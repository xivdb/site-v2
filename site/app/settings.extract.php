<?php
//
// Extract settings
//

// From: https://github.com/ufx/SaintCoinach
define('SAINT_EX_YAML', 'https://raw.githubusercontent.com/ufx/SaintCoinach/master/SaintCoinach/ex.yaml');

// Should the system rip exd to csvs?
define('RIP_TO_CSV', false);

// Which imports should the system do
define('IMPORT_PYTHON', false);
define('IMPORT_EXPLORER', false);
define('IMPORT_SAINT', true);
define('IMPORT_JSON', true);
define('IMPORT_LIBRA', true);

// Some core paths
define('FILE_CONFIG', ROOT_APP .'/config.php');
define('FILE_COMPOSER', ROOT .'/vendor/autoload.php');
define('FILE_LIBRA_SQL', ROOT_EXTRACTS . CURRENT_PATCH . '/libra/app_data.sqlite');
define('FILE_LIBRA_LANGUAGE', ROOT_EXTRACTS . CURRENT_PATCH . '/libra/strings_%s.xml');

// Extract folders
define('EXTRACT_PATH', CURRENT_PATCH);
define('EXTRACT_EXD', '/python/exd/exd/');
define('EXTRACT_EXH', '/python/exh/exd/');
define('EXTRACT_ICONS', '/python/gen/icons/ui/icon/');
define('EXTRACT_MAPS', '/python/gen/maps_icons/ui/map/');
define('EXTRACT_UI', '/python/folder/ui/uld/');
define('EXTRACT_LISTS', '/python/json_lists/');
define('EXPLORER_EXD', '/explorer/exd/');
define('SAINT_EXD', '/saint/exd/');
define('SAINT_MAPS', '/saint/maps/');
define('GARLAND_JSON', '/garland/');
define('CHINESE_EXD', '/chinese/');
define('CHINESE_CNS', 'cns/');
define('CHINESE_CNT', 'cnt/');
define('CHINESE_KO', 'ko/');

define('ICON', '/img/game/{icon}.png');
