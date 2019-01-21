<?php

//
// No put allowed
//
if ($file == '_app' && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT') {
	die('No PUT method allowed (report to kupo@xivdb.com for legitimate issues)');
}

//
// No route on the site accepts PROPFIND, block them.
//
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PROPFIND') {
	die('No PROPFIND method allowed (report to kupo@xivdb.com for legitimate issues)');
}

//
// Block google user content
//
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] == 'http://webcache.googleusercontent.com') {
	die('No Robots (report to kupo@xivdb.com for legitimate issues)');
}

//
// Block Microsoft office protocol
//
if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Microsoft Office Protocol Discovery') {
    die('No Robots (report to kupo@xivdb.com for legitimate issues)');
}
