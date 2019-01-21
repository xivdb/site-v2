<?php
//
// Clearing:
/*
	redis-cli KEYS "translations" | xargs redis-cli DEL
	redis-cli KEYS "character_content_*" | xargs redis-cli DEL
	redis-cli KEYS "extended_content_*" | xargs redis-cli DEL
	redis-cli KEYS "api_list_*" | xargs redis-cli DEL
	redis-cli KEYS "sitemap_*" | xargs redis-cli DEL
	redis-cli KEYS "search_results_*" | xargs redis-cli DEL
	redis-cli KEYS "en_linked_comment_* | xargs redis-cli DEL"
*/

//
// All the cache times!
//
$times =
[
	'CACHE_GAME_CONTENT_DATA' => TIME_1WEEK,
	'CACHE_GAME_CONTENT_LISTS' => TIME_1WEEK,
	'CACHE_GAME_CONTENT_SITEMAP' => TIME_1WEEK,
	'CACHE_GAME_CONTENT_CHARACTERS' => TIME_5MINUTES,
	'CACHE_GAME_CONTENT_FILTERS' => TIME_90DAYS,
	'CACHE_TRANSLATIONS_TIME' => TIME_24HOUR,
	'CACHE_HOMEPAGE' => TIME_5MINUTES,
];

foreach($times as $i => $t) {
	define($i, $t);
}

define('DEFAULT_CACHE_TIME', TIME_60MINUTES);

// enable stuff
define('CACHE_TRANSLATIONS', false);
define('CACHE_CHARACTERS', true);
define('CACHE_GAME_CONTENT', true);
