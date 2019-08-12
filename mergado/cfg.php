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
	new Source(new LaCorrida),
	new Source(new Sono),
	new Source(new MenickaCz(4838, 'Seven bistro', 'https://www.facebook.com/7FoodTrio/', 'seven')),
	new Source(new Zomato(16506537, 'Everest', 'http://everestbrno.cz/index.html', 'everest')),
	new Source(new Zomato(18578705, 'Siwa', 'http://www.siwaorient.cz/', 'siwa')),
	new Source(new Zomato(16506040, 'Šelepka', 'http://www.selepova.cz/denni-menu/', 'selepka')),
	new Source(new ZelenaKocka),
	new Source(new NaberSi),
	new Source(new UMlsnychKocek),
	new Source(new MenickaCz(5350, 'Restaurace U Putchy', 'https://putcha.webnode.cz/', 'putcha')),
	new Source(new Zomato(16507460, 'Viva', 'http://www.pizzerie-viva.cz/', 'pizza')),
];
