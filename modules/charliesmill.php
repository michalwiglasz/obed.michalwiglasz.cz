<?php

class CharliesMill extends LunchMenuSource
{
	public $title = 'Charlie\'s Mill';
	public $link = 'https://www.charliesmill.cz/menu/';
	public $icon = 'charlies';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);

		$today = get_czech_day(date('w', $todayDate));
		$table = $cached['html']->find("div.pricing-box", 0);
		if (!$table) {
			throw new ScrapingFailedException("div.pricing-box not found");
		}

		$withinToday = FALSE;
		foreach ($table->find('div.row') as $row) {
			$l = $row->find('div.text-content', 0)->plaintext;
			$r = $row->find('div.text-content', 1)->plaintext;
			if (!$l) continue;

			if (!$r) {
				if ($withinToday) break;
				$s = mb_strtolower($l, 'utf-8');
				if (strpos($s, $today) !== FALSE)
					$withinToday = TRUE;

			} else if ($withinToday) {
				$s = mb_strtolower($r, 'utf-8');
				if (strpos($s, "cena") !== FALSE) {
					$kind = trim($l).": ";
				} else {
					$what = trim(html_entity_decode($l));
					$price = trim(html_entity_decode($r));
					$quantity = NULL;

					if (preg_match('(^([0-9]+\.)?\s*([0-9,.]+)\s*([gl])\s+(.+?)$)ui', $what, $m)) {
						$what = trim("$m[1] $m[4]");
						$quantity = trim("$m[2] $m[3]");
					}

					$what = $kind.$what; $kind = NULL;
					$result->dishes[] = new Dish($what, $price, $quantity);
				}
			}
		}

		return $result;
	}
}
