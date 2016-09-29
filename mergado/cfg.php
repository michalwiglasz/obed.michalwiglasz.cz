<?php

ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Prague');
header('content-type: text/html; charset=utf-8');

$root = "https://obed.michalwiglasz.cz/mergado";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_menza_interval = 0;
}

$cache_html_interval = $cache_default_interval - 10;


$zomato = [
	'Sono' => [
		'https://www.zomato.com/cs/brno/sono-centrum-restaurant-%C5%BEabov%C5%99esky-brno-sever/denn%C3%AD-menu',
		'http://www.sonocentrum.cz/the-restaurant/denni-menu/',
		'sono',
	],
	'Šelepka' => [
		'https://www.zomato.com/cs/brno/%C5%A1elepka-kr%C3%A1lovo-pole-brno-sever/denn%C3%AD-menu',
		'http://www.selepova.cz/denni-menu/',
		'selepka',
	],
	'Viva' => [
		'https://www.zomato.com/cs/brno/restaurant-viva-%C5%BEabov%C5%99esky-brno-sever/denn%C3%AD-menu',
		'http://www.pizzerie-viva.cz/',
		'viva',
	],
];

$zomato_filters = [
	'(<div class="tmi-price ta-right col-l-2 bold">\\s*<div class="row">\\s*<\/div>\\s*</div>)ui' => '',
	'((\\(|/)(A.)?[0-9,\\s]+(\\)|/)\\s*</div>)' => '</div>',
];
