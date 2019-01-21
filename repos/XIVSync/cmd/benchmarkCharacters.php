<?php
//
// Benchmark character parse
//

require __DIR__ .'/../defines.php';
require __DIR__ .'/../src/misc.php';
require __DIR__ .'/../vendor/autoload.php';
require __DIR__ .'/../maintenance.php';
if (MAINTENANCE) { die('Maintenance'); }
output('XIVSync Auto Update');

// Preload XIVDB Data
require __DIR__ .'/../src/xivdb_preload.php';

(new Sync\Tasks\TaskBenchmarkCharacters())->init();
