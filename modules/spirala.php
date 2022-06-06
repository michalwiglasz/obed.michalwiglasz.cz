<?php

class BioBistroSpirala extends LunchMenuSource
{
	public $title = 'Bio Bistro Spirála';
	public $link = 'http://bio-restaurace.cz/';
	public $sourceLink = 'http://bio-restaurace.cz/?p=menu';
	public $icon = 'spirala';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$result = new LunchMenuResult($cached['stored']);

		$cached = $this->downloadHtml($cacheSourceExpires);
		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		try {
			$content = $cached['html']->find("div.content", 0);
			$source_day = $content->find("h2", 0);

			$regexp = '(' . get_czech_day(date('w', $todayDate)) . ')i';
			if (!preg_match($regexp, $source_day->plaintext)) {
				return $result;
			}

			$soup = $content->find("h4", 0);
			if ($soup && $soup->plaintext == "Polévka" && $soup->next_sibling()) {
				$result->dishes[] = new Dish(html_entity_decode($soup->next_sibling()->plaintext));
			}

			$menu = $content->find("h4", 1);
			if ($menu && $menu->plaintext == "Hlavní jídla" && $menu->next_sibling()) {
				$menu = $menu->next_sibling();

				$num = 1;
				foreach ($menu->find('li') as $el) {
					$what = html_entity_decode($el->plaintext);
					$result->dishes[] = new Dish($what, NULL, NULL, NULL, $num++);
				}
			}
		} catch (Exception $e) {
			throw new ScrapingFailedException($e);
		}

		return $result;
	}
}
