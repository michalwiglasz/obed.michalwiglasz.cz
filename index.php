<?php
require_once dirname(__FILE__) . '/cfg.php';
require_once dirname(__FILE__) . '/lib.php';

header("link: $root/em-ambulance.png; rel=preload", false);
header("link: $root/em-japanese.png; rel=preload", false);
header("link: $root/em-camel.png; rel=preload", false);
header("link: $root/em-velorex.png; rel=preload", false);
header("link: $root/em-nepal.png; rel=preload", false);
header("link: $root/em-monkey.png; rel=preload", false);
header("link: $root/em-italy.png; rel=preload", false);

$today = time();

?>
<!DOCTYPE html>
<title>Jíííídlooooo</title>
<link rel="shortcut icon" href="/favicon.ico">
<html>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width">

<link rel="preload" href="<?= $root ?>/em-ambulance.png" as="image">
<link rel="preload" href="<?= $root ?>/em-japanese.png" as="image">
<link rel="preload" href="<?= $root ?>/em-camel.png" as="image">
<link rel="preload" href="<?= $root ?>/em-velorex.png" as="image">
<link rel="preload" href="<?= $root ?>/em-nepal.png" as="image">
<link rel="preload" href="<?= $root ?>/em-monkey.png" as="image">
<link rel="preload" href="<?= $root ?>/em-italy.png" as="image">

<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<style>
<?php readfile(dirname(__FILE__) . '/style.css') ?>
</style>
<script>
<?php readfile(dirname(__FILE__) . '/script.js') ?>
</script>

<body>
<div id="body">
<!--<img src="GxMLDqy.gif" width="417" height="260">-->

<p class="help">Dvojklikem označ nejlepší jídla a URL pošli kamarádovi!</p>

<?php
/*
$cache_key = 'menza';
if (!cache_html_start($cache_key, 60)) {
		// cache miss
	echo '<h1 class="emoji ambulance"><a href="http://www.kam.vutbr.cz/default.aspx?p=menu&provoz=5">Menza</a></h1>';
	$cached = cache_get_html($cache_key, 'http://www.kam.vutbr.cz/default.aspx?p=menu&provoz=5', 50);
	$data = $cached['html'];
	echo '<p class="retrieved">' . date('j. n. Y H:i:s', $cached['stored']) . '</p>';

	$output = $data->getElementById("m5");
	$output = trim(filter_output($menza_filters, $output));
	if ($output) {
		echo $output;
	} else {
		echo "Buď mají zavřeno, anebo to co dycky.";
	}

	cache_html_end($cache_key);
}
*/

if ($today < $menza_close || $today > $menza_open) {
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

process_zomato($zomato, $cache_default_interval, $cache_html_interval, $zomato_filters);

/* ---------------------------------------------------------------------------*/

$cache_key = 'nepal';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://nepalbrno.cz/weekly-menu/', $cache_html_interval);
	print_header('Nepal', 'http://nepalbrno.cz/weekly-menu/', 'nepal', $cached['stored']);

	$table = $cached['html']->find(".the_content_wrapper table tr");
	$today = strtolower(date('l'));
	/*
	if ($today == 'sunday' || $today == 'saturday') {
		$today = 'monday';
	}
	*/

	$withinToday = FALSE;
	foreach ($table as $tr) {
		$span = $tr->find('span');
		if ($withinToday) {
			if ($span) {
				break; // nothing more to do...
			} else {
				$tds = $tr->find('td');
				// after friday, there are rows with colspan=3
				if (!empty($tds[0]->colspan)) break;

				$what = implode('', $tds[0]->find('text'));
				if (empty($tds[1])) $price = "?";
				else $price = implode('', $tds[1]->find('text'));

				$what = trim(preg_replace('(\\([0-9,]+\\)\\s*$)', '', $what));
				$quantity = NULL;

				if (preg_match('((.+?)([0-9]+g)$)u', $what, $m)) {
					$what = trim($m[1]);
					$quantity = $m[2];
				}

				print_item($what, $price, $quantity);
			}

		} else {
			if ($span) {
				$s = mb_strtolower($span[0], 'utf-8');
				//echo $s, $today;
				if (strpos($s, $today) !== FALSE) {
					$withinToday = TRUE;
					print_subheader((string)($span[0]->find('strong')[0]));
				}
			}
		}
	}

	if (!$withinToday) {
		// Today not found
		echo "Nepovedlo se načíst menu z webu.";
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

$cache_key = 'molino';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://www.molinorestaurant.cz/poledni-menu/', $cache_html_interval);
	print_header('Molino', 'http://www.molinorestaurant.cz/poledni-menu/', 'italy', $cached['stored']);


	// current (or the soonest) day is always first table in the page
	$h1 = $cached['html']->find("#category-content .article-content h1");
	$table = $cached['html']->find("#category-content .article-content table");

	if ($h1) {
		print_subheader(implode('', $h1[0]->find('text')));

		$printed = FALSE;
		foreach ($table[0]->find('tr') as $tr) {
			$tds = $tr->find('td');
			$what = implode('', $tds[0]->find('text'));
			$quantity = implode('', $tds[1]->find('text'));
			$price = implode('', $tds[2]->find('text'));

			print_item($what, $price, $quantity);
			$printed = TRUE;
		}

		if (!$printed) {
			// Today not found
			echo "Nepovedlo se načíst menu z webu.";
		}

	} else {
		// Today not found
		echo "Nepovedlo se načíst menu z webu.";
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/
?>


<h1>Disclaimer</h1>

Tuto stránku vytvořil <a href="http://www.fit.vutbr.cz/~igrochol/">David Grochol</a> během jednoho nudného víkendu (a rozhodně ne během své pracovní doby). <a href="https://michalwiglasz.cz">Michal Wiglasz</a> ji upravil, aby vypadala trochu k světu a nenačítala se půl dne, a propůjčil hosting a doménu.

</div>
</body>
</html>
