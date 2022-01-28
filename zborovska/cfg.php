<?php

$root = "https://obed.obed.eu/zborovska";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute
$cache_html_interval = $cache_default_interval - 10;

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_html_interval = 0;
	$cache_menza_interval = 0;
}

$sources = [
	new Source(new Viva),
	new Source(new LaCorrida),
	new Source(new MenickaCz(2663, 'Korzár', 'http://www.korzar.com/cz/', 'korzar')),
	new Source(new MenickaCz(6023, 'Flames', 'https://www.flames-grill.cz/', 'flames')),
	new Source(new MenickaCz(2749, 'Rubín', 'http://restauracerubin.cz/', 'rubin')),
	new Source(new MenickaCz(6388, 'U Mlsných koček', 'https://umlsnychkocek.metro.bar/', 'umlsnychkocek')),
	new Source(new Zomato(16506040, 'Šelepka', 'http://www.selepova.cz/denni-menu/', 'selepka')),
	new Source(new MenickaCz(3882, 'Naber si', 'http://nabersi.cz/', 'nabersi')),
	new Source(new MenickaCz(3185, 'Zelená kočka', 'https://www.zelenakocka.cz/', 'kocka')),
];
