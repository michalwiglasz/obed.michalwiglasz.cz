<?php

class Seven extends LunchMenuSource {

	public $title = 'Seven bistro';
	public $link = 'https://www.menicka.cz/4838-seven-food.html';
	public $icon = 'seven';

	public function getTodaysMenu($todayDate, $cacheSourceExpires) {

		$cached = $this->downloadHtml($cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		try {
			$result = new LunchMenuResult($cached['stored']);

			$todayBlock = $cached['html']->find("div.obsah div.menicka", 0);

			if (!$todayBlock) {
				throw new ScrapingFailedException("ul.fmenu li.item was not found");
			}

			$dishes = [];
			foreach ($todayBlock->find("div.nabidka_1") as $i => $item) {
				$dishes[$i] = iconv('CP1250', 'utf-8', trim($item->plaintext));
			}

			$prices = [];
			foreach ($todayBlock->find("div.cena") as $i => $item) {
				$prices[$i + 1] = iconv('CP1250', 'utf-8', trim($item->plaintext));
			}

			foreach ($dishes as $i => $dish) {
				$result->dishes[] = new Dish($dish, isset($prices[$i]) ? $prices[$i] : 0);
			}

		} catch (Exception $e) {
			dump($e);
			die;
		}

		return $result;

	}
}
