<?php

ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Prague');
header('content-type: text/html; charset=utf-8');

$root = "https://obed.michalwiglasz.cz/embedit";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_menza_interval = 0;
}

$cache_html_interval = $cache_default_interval - 10;


$zomato = [
];

$zomato_filters = [
	'(<div class="tmi-price ta-right col-l-2 bold">\\s*<div class="row">\\s*<\/div>\\s*</div>)ui' => '',
	'(\\((A.)?[0-9,\\s]+\\)\\s*</div>)' => '</div>',
];
