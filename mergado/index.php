<?php
require_once dirname(__FILE__) . '/cfg.php';
require_once dirname(__FILE__) . '/../lib.php';
require_once dirname(__FILE__) . '/../pdf2text.php';

$today = time();

?>
<!DOCTYPE html>
<title>Jíííídlooooo</title>
<link rel="shortcut icon" href="/favicon.ico">
<html>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width">

<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<style>
<?php readfile(dirname(__FILE__) . '/../style.css') ?>
a {
	color: #69a120;
}

#body {
	margin-bottom: 1em;
}
</style>
<script>
<?php readfile(dirname(__FILE__) . '/../script.js') ?>
</script>

<body>
<div id="body">
<!--<img src="GxMLDqy.gif" width="417" height="260">-->

<p class="help">Dvojklikem označ nejlepší jídla a URL pošli kamarádovi!</p>

<?php

/* ---------------------------------------------------------------------------*/

process_zomato($zomato, $cache_default_interval, $cache_html_interval, $zomato_filters);

/* ---------------------------------------------------------------------------*/

$cache_key = 'kocka';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://www.zelenakocka.cz/', $cache_html_interval);
	print_header('Zelená kočka', 'http://www.zelenakocka.cz/', 'kocka', $cached['stored']);

	$div = $cached['html']->find("div#dnesni-menu", 0);
	if ($div) {
		foreach ($div->find('text') as $item) {
			if ($item->parent()->tag == 'strong') {
				print_subheader($item);
			} else {
				print_item($item);
			}
		}
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

?>

<!--
<h1>Disclaimer</h1>

Základy této stránky položil <a href="http://www.fit.vutbr.cz/~igrochol/">David Grochol</a>. <a href="https://michalwiglasz.cz">Michal Wiglasz</a> ji upravil, aby vypadala trochu k světu a nenačítala se půl dne, propůjčil hosting a doménu a nakonec ji ohnul pro Embedit.
-->
</div>
</body>
</html>
