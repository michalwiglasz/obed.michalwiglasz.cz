<?php

$root = "https://obed.michalwiglasz.cz/besteto";

$sources = [
	new Source(new LaCorrida),
	new Source(new Zomato(16507460, 'Viva', 'http://www.pizzerie-viva.cz/', 'pizza')),
	new Source(new NaberSi),
	new Source(new Zomato(16506537, 'Everest', 'http://everestbrno.cz/index.html', 'everest')),
	new Source(new Sono),
	new Source(new MenickaCz(4838, 'Seven bistro', 'https://www.facebook.com/7FoodTrio/', 'seven')),
	new Source(new MenickaCz(5350, 'Restaurace U Putchy', 'https://putcha.webnode.cz/', 'putcha')),
	new Source(new Zomato(16506040, 'Šelepka', 'http://www.selepova.cz/denni-menu/', 'selepka')),
	new Source(new ZelenaKocka),
	new Source(new Zomato(18397993, 'Zdravý život', 'https://www.zzbrno.cz/menu.htm', 'health')),
	new Source(new Zomato(16507106, 'Oaza', null, 'palm')),
	new Source(new MyKitchen),
];
