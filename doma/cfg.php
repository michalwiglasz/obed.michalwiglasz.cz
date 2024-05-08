<?php

$root = "https://obed.michalwiglasz.cz/doma";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute
$cache_html_interval = $cache_default_interval - 10;

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_html_interval = 0;
	$cache_menza_interval = 0;
}

$sources = [
	new Source(new MenickaCz(6347, 'Restaurace Sup', 'https://www.restauracesup.cz', 'sup')),
	new Source(new MenickaCz(6712, 'Restaurace Dukát', 'https://www.restauracedukat.cz/menu/tydenni-menu/', 'dukat')),
	new Source(new MenickaCz(5843, 'Restaurace & pub Les', 'https://www.lespub.cz/menu', 'les')),
	new Source(new Charlies('Charlie\'s 4est', 'https://www.charlies4est.cz')),
	new Source(new Globus('Globus Brno', 'https://www.globus.cz/brno/sluzby-a-produkty/restaurace')),

	new Source(new Charlies('Charlie\'s Mill', 'https://www.charliesmill.cz')),
	new Source(new MenickaCz(3225, 'U 3 opic', 'http://www.u3opic.cz/', 'u3opic')),
	new Source(new MenickaCz(8483, 'Pad Thai', 'http://www.padthairestaurace.cz', 'thailand')),
	new Source(new Nepal),
	new Source(new MenickaCz(6718, 'Krakonoš', 'https://restauracekrakonos.cz', 'krakonos')),
	new Source(new MenickaCz(2767, 'Yvy Restaurant', 'http://www.yvy.cz/', 'yvy')),
	new Source(new MenickaCz(5335, 'Správné místo', 'http://spravnemisto.cz/', 'spravnemisto')),
	new Source(new MenickaCz(3874, 'U Mušketýra', 'https://www.musketyrbrno.cz/', 'musketyr')),
	new Source(new MenickaCz(4919, 'Borgeska', 'https://www.restauraceborgeska.cz/', 'borgeska')),
	new Source(new KlubCestovatelu),
	new Source(new Bistro53),
	new Source(new BioBistroSpirala),
];
