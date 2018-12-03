<?php

class UMlsnychKocek extends LunchMenuSource {

	public $title = 'U mlsných koček';
	public $link = 'https://umlsnychkocek.metro.bar/';
	public $icon = 'umlsnychkocek';

	protected $dayCounter = null;

	public function getTodaysMenu($todayDate, $cacheSourceExpires) {
		$cached = $this->downloadHtml($cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}
		try {
			$result = new LunchMenuResult($cached['stored']);
			$group = null;

			$today = date('N', $todayDate);

			$div = $cached['html']->find("section.mod-website-about-us div.text-holder", 1);
			$list = explode(PHP_EOL, $div->plaintext);

			$list = array_map(function ($item) {
				return trim($item);
			}, $list);

			$aggregatedData = [];
			foreach ($list as $row) {
				if ($row) {
					$this->isDayHeader($row);
					// Try to parse food
					preg_match("~(Polévka|POLÉVKA)..?(.*)(\*|\+).*~i", $row, $m);
					if ($m) {
						//its a soup
						$aggregatedData[$this->dayCounter] = [
							'soup' => 'Polévka: ' . trim($m[2]),
						];
						continue;
					}
					preg_match("~([N|M]. ?[0-9]|DOPORUČUJEME).*?([0-9]{2,3}\,\-\-).*?([0-9]+ ?g).*?\-(.*)~i", $row, $m);
					if ($m) {
						//its a dish
						$aggregatedData[$this->dayCounter][$m[1]] = [
							'name' => trim($m[4]),
							'price' => trim($m[2]),
							'size' => trim($m[3]),
						];
					}

				}
			}

			foreach ($aggregatedData[$today - 1] as $key => $value) {
				if ($key == 'soup') {
					// Soup
					$result->dishes[] = new Dish(html_entity_decode($value), null, null);
				} else {
					// Dish
					$result->dishes[] = new Dish(html_entity_decode($value['name']), html_entity_decode($value['price']), html_entity_decode($value['size']));
				}
			}
		} catch (Exception $e) {
			throw new ScrapingFailedException($e);
		}

		return $result;

	}

	/**
	 * @param $row
	 * @return bool
	 */
	protected function isDayHeader($row) {

		// Contains name of weekday
		foreach (['pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek'] as $key => $day) {
			//dump($day, mb_strtolower($row), mb_strpos($day, mb_strtolower($row)));
			if (mb_strpos(mb_strtolower($row), $day) !== false) {
				$this->dayCounter = $this->dayCounter !== null ? $this->dayCounter + 1 : 0;
				return true;
			}
		}

		// Contains date
		preg_match("~[0-9]{1,2}\.[0-9]{1,2}\.\.?[0-9]{4}~i", $row, $matches);
		if ($matches) {
			$this->dayCounter = $this->dayCounter !== null ? $this->dayCounter + 1 : 0;
			return true;
		}

		return false;

	}
}
