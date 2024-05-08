<?php

class Charlies extends LunchMenuSource
{
	public $icon = 'charlies';

	public function __construct($title, $link) {
		$this->title = $title;
		$this->link = $link;
		$this->sourceLink = "$link/menu";
	}

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);

		$today = get_czech_day(date('w', $todayDate));

		$content = $cached['html']->find("div.entry-content", 0);
		if (!$content) {
			throw new ScrapingFailedException("div.entry-content not found");
		}

		$tables = $content->find("table.menu-one-day");
		if (!$tables) {
			throw new ScrapingFailedException("table.menu-one-day not found");
		}

		foreach ($tables as $table) {
			$title = $table->find("th.table-title", 0)->plaintext;
			$title_lc = mb_strtolower($title);

			if (strpos($title_lc, $today) !== FALSE) {
				// todays menu
				$this->processTable($result, null, $table);

			} else {
				$isOtherDay = false;
				foreach(get_all_czech_days() as $day) {
					if (strpos($title_lc, $day) !== FALSE) {
						$isOtherDay = true;
						break;
					}
				}

				if (!$isOtherDay) {
					$this->processTable($result, $title, $table);
				}
			}
		}

		return $result;
	}

	protected function processTable(&$result, $group, $table)
	{
		foreach ($table->find('tr') as $tr) {
			if (!$tr->find('td')) {
				continue;
			}

			$what = $tr->find('td', 0)->plaintext;
			$price = $tr->find('td', 1)->plaintext;

			$result->dishes[] = new Dish($what, $price, NULL, $group);
		}
	}
}
