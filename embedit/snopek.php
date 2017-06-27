<?php
$_GET['force'] = TRUE;
require_once dirname(__FILE__) . '/cfg.php';
require_once dirname(__FILE__) . '/../lib.php';
require_once dirname(__FILE__) . '/../pdf2text.php';

$today_timestamp = time();

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
	color: #0080a8;
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

$cache_key = 'snopek';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key . '-home', 'http://www.stravovanisnopek.cz/', $cache_html_interval);
	print_header('Chuťovky paní Snopkové', 'http://www.stravovanisnopek.cz/', 'snopek', $cached['stored']);

	$ok = FALSE;
	do {
		$links = $cached['html']->find(".post-title a");
		if (!count($links)) {
			break;
		}

		$current_week_link = NULL;
		foreach($links as $link) {
			if (startswith($link->href, 'http://www.stravovanisnopek.cz/jidelni-listek')) {
				if (preg_match('/(\d+-\d+-\d+)-do-(\d+-\d+-\d+)/', $link->href, $m)) {
					if (date('W', strtotime($m[1])) == date('W', $today_timestamp)) {
						$current_week_link = $link->href;
						break;
					}
				}
			}
		}

		if (!$current_week_link) {
			break;
		}

		$cached = cache_get_html($cache_key . '-menu', $current_week_link, $cache_html_interval);
		$rows = $cached['html']->find(".post-entry p");
		if (!count($rows)) {
			break;
		}

		$withinToday = FALSE;
		$days = ['PONDĚLÍ', 'ÚTERÝ', 'STŘEDA', 'ČTVRTEK', 'PÁTEK', 'SOBOTA', 'NEDĚLE'];
		$today = $days[date('N') - 1];
		$tomorrow = $days[date('N') % 7];
		$terminator = 'ZMĚNA JÍDELNÍHO LÍSTKU';
		foreach($rows as $row) {
			$row = trim($row->plaintext);
			$row_upper = mb_strtoupper($row);
			if ($withinToday) {
				if ((strpos($row_upper, $tomorrow) !== FALSE)
					|| (strpos($row_upper, $terminator) !== FALSE))
				{
					$withinToday = FALSE;
					$ok = TRUE;
					break;
				}

				$what = preg_replace('#/[0-9,\\s]+/$#', '', $row);
				print_item($what);

			} elseif (strpos($row_upper, $today) !== FALSE) {
				$withinToday = TRUE;
				print_subheader($row);
			}
		}
	} while(FALSE);

	if (!$ok) {
		echo "Nepovedlo se načíst menu z webu.";
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
