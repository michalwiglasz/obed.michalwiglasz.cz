<?php

class Tesar extends LunchMenuSource
{
	public $title = 'U Tesaře';
	public $link = 'http://www.utesare.cz/';
	public $sourceLink = 'http://www.utesare.cz/Menu.pdf';
	public $icon = 'tesar';
	public $note = 'silně experimentální, menu se tahá z PDF (lepší zdroj není)';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadRaw($cacheSourceExpires);
		$today = date('N', $todayDate) - 1;  // 0 = monday, 6 = sunday
		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		// cache miss
		$cached = cache_download($cache_key, 'http://www.utesare.cz/Menu.pdf', $cache_html_interval);

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
				for ($i = 0; $i < strlen($l); $i++) {
					echo $l[$i], "=\x", bin2hex($l[$i]), " ";
				}
			}
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
						$result->dishes[] = $this->process_buffer($buffer, $group);
						$buffer = '';
					} else {
						$buffer .= $line;
					}
				}
				if ($buffer) {
					$result->dishes[] = $this->process_buffer($buffer, $group);
					$buffer = '';
				}
				$menuPrinted = TRUE;
			} elseif (trim($line) == 'Menu týdne:') {
				$group = "Týdenní menu";
				$withinWeekly = TRUE;
				$buffer = '';
			} elseif ($menuPrinted && startswith($line, "HOSTINEC U TESAŘE")) {
				break;
			} elseif ($withinWeekly) {
				$buffer .= $line;
				if (preg_match("([0-9],?-$)", trim($buffer))) {
					$result->dishes[] = $this->process_buffer($buffer, $group);
					$buffer = '';
				}
			}

		} while($line !== FALSE);
		if ($buffer) {
			$result->dishes[] = $this->process_buffer($buffer, $group);
		}

		return $result;
	}

	protected function process_buffer($buffer, $group) {
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
		return new Dish($what, $price, $quantity, $group);
	}
}
