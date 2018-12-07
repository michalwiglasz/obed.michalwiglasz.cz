<?php

class LaCorrida extends LunchMenuSource {

	public $title = 'LaCorrida';
	public $link = 'http://www.lacorrida.cz/zabovresky/';
	public $icon = 'lacorrida';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}
		try {
			$result = new LunchMenuResult($cached['stored']);
			$group = null;

			$today = date('N', $todayDate);

			$div = $cached['html']->find("div.menu-table", 0);

			if (!$div) {
				throw new ScrapingFailedException("div.menu-table was not found");
			}

			foreach ($div->find("div#tabs-{$today} div.menu-item") as $i => $item) {

				$dishName = !!$item->find('.menu-con > p', 1)->plaintext ? $item->find('.menu-con > p', 1)->plaintext : $item->find('.menu-con p', 0)->plaintext;

				if ($i == 0) {
					// Soup
					$result->dishes[] = new Dish(html_entity_decode($dishName));
				} else {
					// Dish
					$result->dishes[] = new Dish(html_entity_decode($dishName), html_entity_decode($item->find('.menu-con h4 span', 0)->plaintext));
				}
			}
		} catch (Exception $e) {
			throw new ScrapingFailedException($e);
		}

		return $result;

	}
}
