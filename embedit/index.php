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

$cache_key = 'myfood';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://www.myfoodmarket.cz/brno-holandska/', $cache_html_interval);
	print_header('My Food', 'http://www.myfoodmarket.cz/brno-holandska/', 'myfood', $cached['stored']);

	$today = date('N') - 1;  // 0 = monday, 6 = sunday

	$soup = $cached['html']->find("div.dny div.jidla ul", $today * 2);
	$dish = $cached['html']->find("div.dny div.jidla ul", $today * 2 + 1);

	function myfood_process_items($parent) {
		foreach ($parent->find('li') as $item) {
			$what = implode('', $item->find('span')[0]->find('text'));
			$price = implode('', $item->find('small')[0]->find('text'));
			print_item($what, $price);
		}
	}

	myfood_process_items($soup);
	myfood_process_items($dish);

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

$cache_key = 'iq';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://www.iqrestaurant.cz/brno/getData.svc?type=brnoMenuHTML2', $cache_html_interval);
	print_header('IQ Restaurant', 'http://www.iqrestaurant.cz/brno/menu.html', 'iq', $cached['stored']);

	$today = date('N') - 1;  // 0 = monday, 6 = sunday

	$todays = $cached['html']->find("dl.menuDayItems", $today * 2);
	$weekly = $cached['html']->find("dl.menuDayItems", $today * 2 + 1);

	/*
	$soupDiv = $todaysDiv->find('div', 0);
	$dishDiv = $todaysDiv->find('div', 1);
	*/

	function iq_process_items($parent) {
		foreach ($parent->children() as $item) {
			$text = implode('', $item->find('text'));

			if ($item->tag == 'dt') {
				$num =  implode('', $item->find('span text'));
				$text = substr($text, strlen($num));
				if (preg_match("((Polévka:\s*)?(.+[lG])\s+(.+)$)ui", $text, $m)) {
					$text = $m[3];
					$quantity = mb_strtolower($m[2]);
				} else {
					$quantity = NULL;
				}
				$what = $text;

			} else if ($item->tag == 'dd') {
				$price = preg_replace('(\\((A.)?[0-9,\\s]+\\)\\s*)', '', $text);
				print_item($what, $price, $quantity);
			}
		}
	}

	iq_process_items($todays);
	if ($weekly) {
		print_subheader("Týdenní menu");
		iq_process_items($weekly);
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

$cache_key = 'tusto';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://titanium.tusto.cz/tydenni-menu/', $cache_html_interval);
	print_header('Tusto', 'http://titanium.tusto.cz/tydenni-menu/', 'tusto', $cached['stored']);

	$today = date('N') - 1;  // 0 = monday, 6 = sunday
	$todays = $cached['html']->find("#rccontent table.menu", $today);

	/*
	$soupDiv = $todaysDiv->find('div', 0);
	$dishDiv = $todaysDiv->find('div', 1);
	*/

	foreach ($todays->find('tr') as $item) {
		$h2 = $item->find('h2');
		if ($h2) {
			continue;
		}

		$what = implode('', $item->find('td', 0)->find('text'));
		$price = implode('', $item->find('td', 2)->find('text'));
		$what = preg_replace('(^[0-9]+\\))', '', $what);
		print_item($what, $price);
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

$cache_key = 'tesar';
if (TRUE || !cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_download($cache_key, 'http://www.utesare.cz/Menu.pdf', $cache_html_interval);
	print_header('U Tesaře', 'http://www.utesare.cz/', 'tesar', $cached['stored'], "silně experimentální, menu se tahá z PDF (lepší zdroj není)");

	$days = ['PONDĚLÍ', 'ÚTERÝ', 'STŘEDA', 'ČTVRTEK', 'PÁTEK', 'SOBOTA', 'NEDĚLE'];
	$today = $days[date('N') - 1];
	$tomorrow = $days[date('N') % 7];

	$a = new PDF2Text();
	$a->setContents($cached['contents']);
	$a->decodePDF();
	$output = $a->output(); // iconv('iso-8859-2', 'utf-8', $a->output());

	$encoding_fixes = [
		"\xc1" => "Á",
		"\xda" => "Ú",
		"\xdd" => "Ý",
		"\xcd" => "Í",

		"\xe1" => "á",
		"\xe9" => "é",
		"\xed" => "í",
		"\xfd" => "ý",
	];

	$output = strtr($output, $encoding_fixes);
	$lines = explode("\n", $output);
	foreach ($lines as $l) {
		if (json_encode($l) == "null") {
			dump($l);
			for ($i = 0; $i < strlen($l); $i++) {
				echo $l[$i], "=\x", bin2hex($l[$i]), " ";
			}
		}
	}

	function process_buffer($buffer) {
		$quantity = NULL;
		if (preg_match('(^\\s*(.+?)([0-9]+,?-)\\s*$)ui', $buffer, $m)) {
			$what = $m[1];
			$price = $m[2];
		} else {
			$what = $buffer;
			$price = NULL;
		}
		if (preg_match("(^([0-9]+[g])\s+(.+)$)ui", $what, $m)) {
			$what = $m[2];
			$quantity = mb_strtolower($m[1]);
		}
		$what = str_replace("\n", "", $what);
		print_item($what, $price, $quantity);
	}

	reset($lines);
	$menuPrinted = FALSE;
	$withinWeekly = FALSE;
	do {
		$line = next($lines);
		if (startswith($line, $today)) {
			$buffer = '';
			while ($line !== FALSE) {
				$line = next($lines);
				//dump($line);
				if (startswith($line, $tomorrow)) break;
				if ($line === FALSE) break;
				if (startswith($line, 'Jídla týdenní nabídky')) break;
				if (preg_match("(^[0-9]+\\.$)u", $line)) {
					process_buffer($buffer);
					$buffer = '';
				} else {
					$buffer .= $line;
				}
			}
			if ($buffer) process_buffer($buffer);
			$menuPrinted = TRUE;
		} elseif (trim($line) == 'Menu týdne:') {
			print_subheader("Týdenní menu");
			$withinWeekly = TRUE;
			$buffer = '';
		} elseif ($menuPrinted && startswith($line, "HOSTINEC U TESAŘE")) {
			break;
		} elseif ($withinWeekly) {
			$buffer .= $line;
			if (preg_match("([0-9],?-$)", trim($buffer))) {
				process_buffer($buffer);
				$buffer = '';
			}
		}

	} while($line !== FALSE);
	if ($buffer) process_buffer($buffer);
/*
	for ($i = 0; $i < count($lines); $i++) {
		$line = trim($lines[$i]);
		if ($line == $today) {
			$buffer = '';
			while ($line != $tomorrow) {
				$i++;
				$buffer .= trim($lines[$i]);
			}
			echo $buffer;
		}
	}
*/
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
