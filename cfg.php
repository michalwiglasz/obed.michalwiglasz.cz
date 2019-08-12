<?php

$root = "https://obed.michalwiglasz.cz/fit";
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
	new Source(new Zomato(16506890, 'Camel', 'http://www.restaurace-camel.com/', 'camel')),
	new Source(new Zomato(16505998, 'U 3 opic', 'http://www.u3opic.cz/', 'monkey')),
	new Source(new Zomato(16506806, 'Pad Thai', 'http://padthairestaurace.cz/', 'thailand')),
	new Source(new Nepal),
	new Source(new Bistro53),
	new Source(new Velorex), //Zomato(16506807, 'Velorex', 'http://www.restauracevelorex.cz/', 'velorex')),
	new Source(new Zomato(16505880, 'Yvy Restaurant', 'http://www.yvy.cz/', 'yvy')),
	new Source(new CharliesMill),
	//new Source(new Zomato(18318157, 'Semilasso', 'http://restaurace-semilasso.cz/', 'semilasso')),
	//new Source(new Kralovska),
	new Source(new KlubCestovatelu),
	new Source(new MenickaCz(5335, 'Správné místo', 'http://spravnemisto.cz/', 'spravnemisto')),
];


if (get_today_timestamp() < $menza_close || get_today_timestamp() > $menza_open) {
	$sources[] = new Source(new Menza, 60);
}
