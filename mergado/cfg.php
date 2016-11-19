<?php

$root = "https://obed.michalwiglasz.cz/mergado";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute
$cache_html_interval = $cache_default_interval - 10;

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_html_interval = 0;
	$cache_menza_interval = 0;
}

$sources = [
	new Source(new Zomato(16507390, 'Sono', 'http://www.sonocentrum.cz/the-restaurant/denni-menu/', 'sono')),
	new Source(new Zomato(16507460, 'Viva', 'http://www.pizzerie-viva.cz/', 'pizza')),
	new Source(new Zomato(16506040, 'Šelepka', 'http://www.selepova.cz/denni-menu/', 'selepka')),
	new Source(new ZelenaKocka),
];
