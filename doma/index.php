<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');
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
h1, a {
	color: #e0218a;
}

#panel-picker {
    color: #e0218a;
}

#panel-picker-menu li:hover {
    color: #e0218a;
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

print_footer();

?>
</div>
</body>
</html>
