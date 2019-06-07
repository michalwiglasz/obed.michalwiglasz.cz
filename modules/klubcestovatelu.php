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

		$regexp = '(' . get_czech_day(date('w', $todayDate)) . '\s+' . date('j', $todayDate) . '\.)ui';

		$pricearr = array();
		$prices = $cached['html']->find("div.article-content ol li", 0);
		if ($prices && !$prices->prev_sibling() && !$prices->next_sibling()) {
			$prices = explode('/', $prices->plaintext);
			foreach ($prices as $price) {
				$price = explode('.', $price);
				$pricearr[] = trim(ltrim($price[1], " –-"));
			}
		}

		$days = $cached['html']->find("div.article-content p strong span");
		foreach ($days as $day) {
			if (!preg_match($regexp, $day->plaintext)) continue;

			$today = $day->find_ancestor_tag('p');
			$soup = $today->next_sibling();
			$menu = $soup->next_sibling();
			while ($menu->tag != "ol" && $menu) {
				$menu = $menu->next_sibling();
			}

			if ($soup && $soup->tag == 'p' && trim(str_replace("\xc2\xa0", ' ', html_entity_decode($soup->plaintext))) != NULL) {
				$result->dishes[] = new Dish(html_entity_decode($soup->plaintext));
			}

			if ($menu && $menu->tag == 'ol') {
				$num = 1;
				foreach ($menu->find('li') as $dish) {
					$what = html_entity_decode($dish->plaintext);
					$price = isset($pricearr[$num-1]) ? $pricearr[$num-1] : NULL;
					$result->dishes[] = new Dish($what, $price, NULL, NULL, $num++);
				}
			}
		}

		return $result;
	}
}
