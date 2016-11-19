<?php

class ZelenaKocka extends LunchMenuSource
{
	public $title = 'Zelená kočka';
	public $link = 'http://www.zelenakocka.cz/';
	public $icon = 'kocka';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = cache_get_html($this->title, $this->link, $cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$div = $cached['html']->find("div#dnesni-menu", 0);
		if (!$div) {
			throw new ScrapingFailedException("div#dnesni-menu not found");
		}

		foreach ($div->find('text') as $item) {
			$plaintext = trim($item->plaintext);
			if (!$plaintext) continue;
			if ($item->parent()->tag == 'strong') {
				$group = $plaintext;
			} else {
				//echo $item;
				$result->dishes[] = new Dish($plaintext, NULL, NULL, $group);
			}
		}

		return $result;
	}
}
