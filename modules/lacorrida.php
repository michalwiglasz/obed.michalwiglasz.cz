<?php

class LaCorrida extends LunchMenuSource {

	public $title = 'LaCorrida';
	public $link = 'http://www.lacorrida.cz/zabovresky/denni-menu';
	public $icon = 'lacorrida';

	public function getTodaysMenu($todayDate, $cacheSourceExpires) {

		$cached = cache_get_html($this->title, $this->link, $cacheSourceExpires, false);
		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$today = date('N', $todayDate) - 1;

		$div = $cached['html']->find("div.field-collection-view", $today);

		if (!$div) {
			throw new ScrapingFailedException("div.field-collection-view was not found");
		}

		foreach ($div->find('table.field-collection-view-final tr') as $i => $item) {
			if ($i == 0) {
				continue;
			} elseif ($i == 1) {
				// Soup
				$result->dishes[] = new Dish($item->find('.field_nazev', 0)->plaintext, NULL, NULL, $group);
			} else {
				// Dish
				$result->dishes[] = new Dish($item->find('.field_nazev', 0)->plaintext, $item->find('.field_cena', 0)->plaintext, NULL, $group);
			}
		}

		return $result;

	}
}