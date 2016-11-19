<?php

class Nepal extends LunchMenuSource
{
	public $title = 'Nepal';
	public $link = 'http://nepalbrno.cz/weekly-menu/';
	public $icon = 'nepal';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = cache_get_html($this->title, $this->link, $cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		$table = $cached['html']->find(".the_content_wrapper table", 0);
		$today = mb_strtolower(date('l', $todayDate));
		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$withinToday = FALSE;
		foreach ($table->find('tr') as $tr) {
			$span = $tr->find('span', 0);
			if ($withinToday) {
				if ($span) {
					// monday is seen twice, so we check it really is different day
					$s = mb_strtolower($span->plaintext, 'utf-8');
					if (strpos($s, $today) === FALSE) {
						return $result; // nothing more to do...
					}

				} else {
					$tds = $tr->find('td');
					// after friday, there are rows with colspan=3
					if (!empty($tds[0]->colspan)) break;

					$what = implode('', $tds[0]->find('text'));
					if (empty($tds[1])) $price = "?";
					else $price = implode('', $tds[1]->find('text'));

					$what = trim(preg_replace('(\\([0-9,]+\\)\\s*$)', '', $what));
					$quantity = NULL;

					if (preg_match('((.+?)([0-9]+g)$)u', $what, $m)) {
						$what = trim($m[1]);
						$quantity = $m[2];
					}

					$result->dishes[] = new Dish($what, $price, $quantity, $group);
				}

			} else {
				if ($span) {
					$s = mb_strtolower($span->plaintext, 'utf-8');
					//echo $s, $today;
					if (strpos($s, $today) !== FALSE) {
						//echo "setting to yes", $s, $today;
						$withinToday = TRUE;
						$day = $span->plaintext;
						$group = str_replace("/", " / ", $day);
						continue;
					}
				}
			}
		}
	}
}
