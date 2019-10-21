<?php

$root = "https://obed.michalwiglasz.cz/";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute
$cache_html_interval = $cache_default_interval - 10;

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_html_interval = 0;
	$cache_menza_interval = 0;
}


$menza_close = strtotime('2018-06-15 23:59:59');
$menza_open = strtotime('2018-09-17 00:00:01');


$sources = [
	new Source(new Zomato(16506128, ' Onyx', 'https://www.facebook.com/pages/Restaurace-Onyx/161675097220650', 'onyx')),
	new Source(new Zomato(16511903, ' Styl Pub', 'https://www.facebook.com/pages/category/Restaurant/STYL-PUB-643338649032733/', 'styl')),
	new Source(new Zomato(16506253, 'Svitavská rychta', 'http://www.svitavskarychta.cz/', '')),
	new Source(new MenickaCz(4884, 'Steak House K1', 'http://www.steakhousek1.cz/www/restaurace-akce.php', 'k1')),
	new Source(new MenickaCz(5363, 'Goa', 'http://www.restaurant-goa.cz/', 'goa')),
];
