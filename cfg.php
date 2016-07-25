<?php

ini_set('display_errors', 'on');
header('content-type: text/html; charset=utf-8');

$root = "https://obed.michalwiglasz.cz";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_menza_interval = 0;
}

$cache_html_interval = $cache_default_interval - 10;

$menza_close = strtotime('2016-06-30 23:59:59');
$menza_open = strtotime('2016-09-19 00:00:01');


$zomato = [
	'Camel' => [
	'https://www.zomato.com/cs/Camel1/menu#tabtop',
	'https://www.zomato.com/cs/Camel1/menu#tabtop',
	'camel',
	],
	'U 3 opic' => [
	'https://www.zomato.com/cs/brno/u-3-opic-kr%C3%A1lovo-pole-brno-sever/menu#tabtop',
	'https://www.zomato.com/cs/brno/u-3-opic-kr%C3%A1lovo-pole-brno-sever/menu#tabtop',
	'monkey',
	],
	'Velorex' => [
	'https://www.zomato.com/cs/brno/velorex-kr%C3%A1lovo-pole-brno-sever/menu#daily',
	'https://www.zomato.com/cs/brno/velorex-kr%C3%A1lovo-pole-brno-sever/menu#daily',
	'velorex',
	],
	'Pad Thai' => [
	'https://www.zomato.com/cs/brno/pad-thai-kr%C3%A1lovo-pole-brno-sever/menu#daily',
	'https://www.zomato.com/cs/brno/pad-thai-kr%C3%A1lovo-pole-brno-sever/menu#daily',
	'japanese',
	],
];

$zomato_filters = [
	'(<div class="tmi-price ta-right col-l-2 bold">\\s*<div class="row">\\s*<\/div>\\s*</div>)ui' => '',
	'(\\((A.)?[0-9,\\s]+\\)\\s*</div>)' => '</div>',
];

$menza_filters = [
	'(&nbsp;)' => ' ',
	'(<td class="levy">[HP]\\s+)' => '<td class="levy">',
	'((<td class="levyjid[^"]+"[^>]+>)P\s)ui' => '$1Polévka ',
	'(<small style=\'font-size: 8pt;\'>[^>]+</small>)' => '',
	'(<td class="levy"><small> </span></td>)' => '',
];
