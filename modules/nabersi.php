<?php

class NaberSi extends LunchMenuSource {

	public $title = 'Nabersi';
	public $link = 'http://nabersi.cz/';
	public $icon = 'nabersi';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);
		$group = null;

		$div = $cached['html']->find("div.su-tabs-panes .su-tabs-pane", 0);

		if (!$div) {
			throw new ScrapingFailedException("div.menu-table was not found");
		}

		foreach ($div->find("table td > *") as $i => $item) {
			if ($item->plaintext != '&nbsp;') {

				if (substr($item->plaintext, -1) == ":") {
					$group = $item->plaintext;
				} else {
					$result->dishes[] = new Dish($item->plaintext, null, null, $group);
				}
			}
		}

		return $result;

	}
}
