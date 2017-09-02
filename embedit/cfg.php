<?php

$root = "https://obed.michalwiglasz.cz/embedit";
$cache_default_interval = 60 * 60; // 1 hour
$cache_menza_interval = 60;  // 1 minute
$cache_html_interval = $cache_default_interval - 10;

if (isset($_GET['force'])) {
	$cache_default_interval = 0;
	$cache_html_interval = 0;
	$cache_menza_interval = 0;
}

$sources = [
    new Source(new MyFood),
    new Source(new IqRestaurant),
    new Source(new Tesar),
    /*
    new Source(new Snopek),
	new Source(new Tusto),
    */
];
