<?php
// test for maintenance
$status = intval(file_get_contents(__DIR__.'/maintenance.status'));
define('MAINTENANCE', $status);
