<?php

require_once dirname(__FILE__) . '/lib.php';
require_once dirname(__FILE__) . '/cfg.php';


$menus = collect_menus($sources, $cache_default_interval);

if (isset($_GET['json'])) {
	print_json($root, $menus);
	die;
}

header('content-type: text/html; charset=utf-8');
print_html_head($root);
?>
<body>
<div id="body">
<?php

/* ---------------------------------------------------------------------------*/

print_infobox();

/* ---------------------------------------------------------------------------*/

print_html($root, $menus);

/* ---------------------------------------------------------------------------*/

if (get_today_timestamp() < $menza_close || get_today_timestamp() > $menza_open) {
	print_header('Menza', 'http://www.kam.vutbr.cz/default.aspx?p=menu&provoz=5', 'ambulance', time());

	$data = file_get_html('http://www.kam.vutbr.cz/default.aspx?p=menu&provoz=5');
	$output = $data->getElementById("m5");
	$output = trim(filter_output($menza_filters, $output));
	if ($output) {
		echo $output;
	} else {
		echo "Buď mají zavřeno, anebo to co dycky.";
	}
}

/* ---------------------------------------------------------------------------*/
?>


<h1>Disclaimer</h1>

Tuto stránku vytvořil <a href="http://www.fit.vutbr.cz/~igrochol/">David Grochol</a> během jednoho nudného víkendu (a rozhodně ne během své pracovní doby). <a href="https://michalwiglasz.cz">Michal Wiglasz</a> ji upravil, aby vypadala trochu k světu a nenačítala se půl dne, a propůjčil hosting a doménu. Máme i <a href="?json">JSON</a> pro strojové zpracování.

</div>
</body>
</html>
