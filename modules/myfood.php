<?php

class MyFood extends LunchMenuSource
{
	public $title = 'My Food';
	public $link = 'http://www.myfoodmarket.cz/brno-holandska/';
	public $icon = 'myfood';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$today = date('N', $todayDate); - 1;  // 0 = monday, 6 = sunday
		$result = new LunchMenuResult($cached['stored']);

		$div = $cached['html']->find("div.dny div.jidla", 0);
		if (!$div) {
			throw new ScrapingFailedException("div.dny div.jidla not found");
		}

		$divToday =  $div->children($today);
		if (!$divToday) {
			throw new ScrapingFailedException("div.dny div.jidla no. $today not found");
		}

		foreach ($divToday->find('li') as $item) {
			$what = $item->find('span')[0]->plaintext;
			$price = $item->find('small')[0]->plaintext;
			$result->dishes[] = new Dish($what, $price);
		}

		return $result;
	}
}
