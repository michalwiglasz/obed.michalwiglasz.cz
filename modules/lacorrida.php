<?php

class LaCorrida extends LunchMenuSource {

	public $title = 'LaCorrida';
	public $link = 'http://www.lacorrida.cz/zabovresky/';
	public $icon = 'lacorrida';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);

		$result = new LunchMenuResult($cached['stored']);
		$group = null;

		$today = date('N', $todayDate);

		$div = $cached['html']->find("div.menu-table", 0);

		if (!$div) {
			throw new ScrapingFailedException("div.menu-table was not found");
		}

		foreach ($div->find("div#tabs-{$today} div.col-sm-6") as $i => $item) {
			if ($i == 0) {
				// Soup
				$result->dishes[] = new Dish(html_entity_decode($item->find('.menu-con p', 0)->plaintext));
			} else {
				// Dish
				$result->dishes[] = new Dish(html_entity_decode($item->find('.menu-con p', 0)->plaintext), html_entity_decode($item->find('.menu-con h4 span', 0)->plaintext));
			}
		}

		return $result;

	}
}
