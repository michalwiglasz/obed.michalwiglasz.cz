<?php

$root = "https://obed.michalwiglasz.cz/besteto";

$sources = [
	new Source(new LaCorrida),
	new Source(new Zomato(16507460, 'Viva', 'http://www.pizzerie-viva.cz/', 'pizza')),
	new Source(new NaberSi),
	new Source(new Zomato(16506537, 'Everest', 'http://everestbrno.cz/index.html', 'everest')),
	new Source(new Sono),
	new Source(new MenickaCz('Seven bistro', 'https://www.menicka.cz/4838-seven-food.html', 'seven')),
	new Source(new MenickaCz('Restaurace U Putchy', 'https://www.menicka.cz/5350-restaurace-u-putchy-.html', 'putcha')),
	new Source(new Zomato(16506040, 'Šelepka', 'http://www.selepova.cz/denni-menu/', 'selepka')),
	new Source(new ZelenaKocka),
	new Source(new Zomato(18397993, 'Zdravý život', 'https://www.zzbrno.cz/menu.htm', 'health')),
	new Source(new Zomato(16507106, 'Oaza', null, 'palm')),
	new Source(new MyKitchen),
];
