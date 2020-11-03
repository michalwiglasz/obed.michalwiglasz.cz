<?php

class Viva extends LunchMenuSource {

	public $title = 'Viva';
	public $link = 'https://www.pizzerie-viva.cz/';
	public $icon = 'viva';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		try {
			$div = $cached['html']->find("div.menus-carousel div.item div.menu-1", 0);
			if (!$div) {
				throw new ScrapingFailedException("div.owl-wrapper was not found");
			}

			foreach ($div->find("ul li") as $i => $item) {
				$what = $item->find('div.menu-item', 0)->plaintext;
				$price = $item->find('div.menu-item-price', 0)->plaintext;

				$result->dishes[] = new Dish($what, $price, NULL, $group);
			}
		} catch (Exception $e) {
			throw new ScrapingFailedException($e);
		}

		return $result;

	}
}
