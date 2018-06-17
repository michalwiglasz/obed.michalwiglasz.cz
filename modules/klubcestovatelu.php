<?php

class KlubCestovatelu extends LunchMenuSource
{
	public $title = 'Klub cestovatelů';
	public $link = 'https://www.hedvabnastezka.cz/klub-cestovatelu-brno/poledni-menu-2/';
	public $icon = 'klubcestovatelu';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);

		$today = date('N', $todayDate) - 1;  // 0 = monday, 6 = sunday
		$menu = $cached['html']->find("div.article-content div ol", $today);
		if (!$menu) {
			throw new ScrapingFailedException("div.article-content div ol not found");
		}

		$pricearr = array();
		$prices = $cached['html']->find("div.article-content p", 3);
		if ($prices) {
			$prices = explode('/', $prices->plaintext);
			foreach ($prices as $price) {
				$price = explode('.', $price);
				$pricearr[] = trim(ltrim($price[1], " –-"));
			}
		}

		$soup = $menu->prev_sibling();
		if ($soup && trim(str_replace("\xc2\xa0", ' ', html_entity_decode($soup->plaintext))) != NULL) {
			$result->dishes[] = new Dish(html_entity_decode($soup->plaintext));
		}

		$num = 1;
		foreach ($menu->find('li') as $dish) {
			$result->dishes[] = new Dish(html_entity_decode($dish->plaintext), isset($pricearr[$num-1]) ? $pricearr[$num-1] : NULL, NULL, NULL, $num++);
		}

		return $result;
	}
}
