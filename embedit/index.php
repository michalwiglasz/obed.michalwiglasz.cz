<?php
require_once dirname(__FILE__) . '/lib.php';
require_once dirname(__FILE__) . '/pdf2text.php';
require_once dirname(__FILE__) . '/cfg.php';

header('content-type: text/html; charset=utf-8');
print_html_head($root);
?>
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

print_infobox();

/* ---------------------------------------------------------------------------*/

process_zomato($zomato, $cache_default_interval, $cache_html_interval, $zomato_filters);

/* ---------------------------------------------------------------------------*/

$cache_key = 'myfood';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://www.myfoodmarket.cz/brno-holandska/', $cache_html_interval);
	print_header('My Food', 'http://www.myfoodmarket.cz/brno-holandska/', 'myfood', $cached['stored']);

	$today = date('N') - 1;  // 0 = monday, 6 = sunday

	if ($cached['html']) {
		$menu = $cached['html']->find("div.dny div.jidla", 0)->children($today);

		if ($menu) {
			foreach ($menu->find('li') as $item) {
				$what = implode('', $item->find('span')[0]->find('text'));
				$price = implode('', $item->find('small')[0]->find('text'));
				print_item($what, $price);
			}
		} else {
			echo "Nepovedlo se načíst menu z webu.";
		}
	} else {
		echo "Nepovedlo se načíst menu z webu.";
	}

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
				if (preg_match("((Polévka:\s*)?([0-9,.]+\\s*+[lG])\s+(.+)$)ui", $text, $m)) {
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

	if ($todays) {
		iq_process_items($todays);
	} else {
		echo "Nepovedlo se načíst menu z webu.";
	}
	if ($weekly) {
		print_subheader("Týdenní menu");
		iq_process_items($weekly);
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

$cache_key = 'snopek';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key . '-home', 'http://www.stravovanisnopek.cz/', $cache_html_interval);
	print_header('Stravování Snopek', 'http://www.stravovanisnopek.cz/', 'snopek', $cached['stored']);

	$ok = FALSE;
	do {
		if (!$cached['html']) {
			break;
		}

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
		if (preg_match("(^([0-9]+\s*(?:g|ks))\s+(.+)$)ui", $what, $m)) {
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
			if ($buffer) {
				process_buffer($buffer);
				$buffer = '';
			}
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

$cache_key = 'tusto';
if (!cache_html_start($cache_key, $cache_default_interval)) {
	// cache miss
	$cached = cache_get_html($cache_key, 'http://titanium.tusto.cz/tydenni-menu/', $cache_html_interval);
	print_header('Tusto', 'http://titanium.tusto.cz/tydenni-menu/', 'tusto', $cached['stored']);

	$today = date('N') - 1;  // 0 = monday, 6 = sunday
	if ($cached['html']) {
		$todays = $cached['html']->find("#rccontent table.menu", $today);

		/*
		$soupDiv = $todaysDiv->find('div', 0);
		$dishDiv = $todaysDiv->find('div', 1);
		*/
		if ($todays) {
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
		} else {
			echo "Nepovedlo se načíst menu z webu.";
		}
	} else {
		echo "Nepovedlo se načíst menu z webu.";
	}

	cache_html_end($cache_key);
}

/* ---------------------------------------------------------------------------*/

print_footer();

?>
</div>
</body>
</html>
