<?php

require_once dirname(__FILE__) . '/../lib.php';
require_once dirname(__FILE__) . '/cfg.php';

$menus = collect_menus($sources, $cache_default_interval);

if (isset($_GET['json'])) {
	print_json($root, $menus);
	die;
}

header('content-type: text/html; charset=utf-8');
print_html_head($root);
?>

<style>
a {
	color: #69a120;
}

#body {
	margin-bottom: 1em;
}
</style>

<body>
<div id="body">
<?php

/* ---------------------------------------------------------------------------*/

print_infobox();

if (isset($_GET['gif'])) {
	echo '<img src="GxMLDqy.gif" width="417" height="260">';
}

/* ---------------------------------------------------------------------------*/

print_html($root, $menus);

/* ---------------------------------------------------------------------------*/

?>

<!--
<h1>Disclaimer</h1>

Základy této stránky položil <a href="http://www.fit.vutbr.cz/~igrochol/">David Grochol</a>. <a href="https://michalwiglasz.cz">Michal Wiglasz</a> ji upravil, aby vypadala trochu k světu a nenačítala se půl dne, propůjčil hosting a doménu a nakonec ji ohnul pro Embedit.
-->
</div>
</body>
</html>
