<?php

class IqRestaurant extends LunchMenuSource
{
	public $title = 'IQ Restaurant';
	public $link = 'http://www.iqrestaurant.cz/brno/menu.html';
	public $sourceLink = 'http://www.iqrestaurant.cz/brno/getData.svc?type=brnoMenuHTML2';
	public $icon = 'iq';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$today = date('N', $todayDate); - 1;  // 0 = monday, 6 = sunday
		$result = new LunchMenuResult($cached['stored']);

		$todays = $cached['html']->find("dl.menuDayItems", $today * 2);
		$weekly = $cached['html']->find("dl.menuDayItems", $today * 2 + 1);

		if (!$todays) {
			throw new ScrapingFailedException("todays menu not found");
		}

		foreach ($this->parseGroup($todays) as $dish) {
			$result->dishes[] = $dish;
		}

		if ($weekly) {
			foreach ($this->parseGroup($weekly, "Týdenní menu") as $dish) {
				$result->dishes[] = $dish;
			}
		}

		return $result;
	}

	protected function parseGroup($parent, $groupName = null) {
		$result = array();

		foreach ($parent->children() as $item) {
			$text = $item->plaintext;

			if ($item->tag == 'dt') {
				$num =  implode('', $item->find('span text'));
				$text = substr($text, strlen($num));
				if (preg_match("((Polévka:\s*)?([0-9,.]+\\s*+[lG])\s+(.+)$)ui", $text, $m)) {
					$text = $m[3];
					$quantity = mb_strtolower($m[2]);
				} else {
					$quantity = NULL;
				}
				$what = $text;

			} else if ($item->tag == 'dd') {
				$price = preg_replace('(\\((A.)?[0-9,\\s]+\\)\\s*)', '', $text);
				$result[] = new Dish($what, $price, $quantity, $groupName);
			}
		}

		return $result;
	}
}
