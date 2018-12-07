<?php

class MenickaCz extends LunchMenuSource {

	public function __construct($title, $link, $icon) {
		$this->title = $title;
		$this->link = $link;
		$this->icon = $icon;
	}

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
			throw new ScrapingFailedException($e);
		}

		return $result;

	}
}
