<?php

class Sono extends LunchMenuSource {

	public $title = 'Sono';
	public $link = 'http://www.hotel-brno-sono.cz/restaurace/';
	public $icon = 'sono';

	public function getTodaysMenu($todayDate, $cacheSourceExpires) {
		$cached = $this->downloadHtml($cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}
		try {
			$result = new LunchMenuResult($cached['stored']);
			$today = date('N', $todayDate);

			$todayBlock = $cached['html']->find("ul.fmenu li.item", $today - 1);

			if (!$todayBlock) {
				throw new ScrapingFailedException("ul.fmenu li.item was not found");
			}

			$soup = trim($todayBlock->find('div.item-soup', 0)->plaintext);
			if ($soup) {
				$result->dishes[] = new Dish($soup);
			}

			foreach ($todayBlock->find("ul.foodlist li") as $i => $item) {

				$dishName = $item->find('p', 0)->plaintext;
				$price = $item->find('span', 0)->plaintext;;
				// Dish
				if ($dishName) {
					$result->dishes[] = new Dish($dishName, $price);
				}

			}
		} catch (Exception $e) {
			dump($e);
			die;
		}

		return $result;

	}
}
