<?php

class MyKitchen extends LunchMenuSource {

	public $title = 'MyKitchen';
	public $link = 'https://www.my-kitchen.cz/kategorie-produktu/denni-menu/';
	public $icon = 'mykitchen';

	public function getTodaysMenu($todayDate, $cacheSourceExpires) {
		$cached = $this->downloadHtml($cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}
		try {
			$result = new LunchMenuResult($cached['stored']);
			$today = (int)date('N', $todayDate);

			$lunchTable = $cached['html']->find("table.obedy", 0);

			if (!$lunchTable) {
				throw new ScrapingFailedException("table.obedy not found.");
			}

			$currentDay = 0;
			$currentDayRow = 0;

			$meals = [];
			$soups = [];

			foreach ($lunchTable->find("tr") as $i => $tr) {
				if ($tr->id) {
					$currentDay++;
					$currentDayRow = 0;
					continue;

				} else {
					$currentDayRow++;
				}

				if ($currentDay === $today && trim($tr->plaintext)) {
					$mealName = $tr->find('td', 0)->plaintext;
					$soupName = $tr->find('td', 1)->plaintext;
					$price = $tr->find('td', 2)->find('strong', 0)->plaintext;

					if (!isset($meals[$mealName])) {
						$meals[$mealName] = [];
					}

					if (mb_strtolower($soupName) === 'bez polévky') {
						$meals[$mealName]['withoutSoup'] = $price;

					} else {
						$meals[$mealName]['withSoup'] = $price;
						$soups[$soupName] = true;
					}
				}
			}

			foreach ($soups as $name => $_) {
				$result->dishes[] = new Dish($name);
			}

			foreach ($meals as $name => $prices) {
				$prices = array_filter([$prices['withoutSoup'], $prices['withSoup']]);
				$prices = implode(' / ', $prices);
				$result->dishes[] = new Dish($name, $prices);
			}

		} catch (Exception $e) {
			throw new ScrapingFailedException($e);
		}

		return $result;

	}
}
