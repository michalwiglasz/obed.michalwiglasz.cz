<?php

class Nepal extends LunchMenuSource
{
	public $title = 'Nepal';
	public $link = 'https://nepalbrno.cz/weekly-menu/';
	public $icon = 'nepal';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);

		$table = $cached['html']->find(".the_content_wrapper table", 2);
		$today = mb_strtolower(date('l', $todayDate));
		$group = NULL;

		if (!$table) {
			throw new ScrapingFailedException(".the_content_wrapper table not found");
		}

		$withinToday = FALSE;
		foreach ($table->find('tr') as $tr) {
			$span = $tr->find('span', 0);
			if ($withinToday) {
				if ($span) {
					// monday is seen twice, so we check it really is different day
					$s = mb_strtolower($span->plaintext, 'utf-8');
					if (strpos($s, $today) === FALSE) {
						break; // nothing more to do...
					}

				} else {
					$tds = $tr->find('td');
					// after friday, there are rows with colspan=3
					if (!empty($tds[0]->colspan)) break;

					$what = implode('', $tds[0]->find('text'));
					if (empty($tds[1])) $price = "?";
					else $price = implode('', $tds[1]->find('text'));

					$what = str_replace("\xc2\xa0", "\x20", $what); // replace UTF-8 non-breaking spaces
					$what = trim(preg_replace('(\\([0-9,]+\\)\\s*$)', '', $what));

					if (!empty($what))
						$result->dishes[] = new Dish($what, $price, NULL, $group);
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

		return $result;
	}
}
