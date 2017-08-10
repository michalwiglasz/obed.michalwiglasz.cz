<?php

class Molino extends LunchMenuSource
{
	public $title = 'Molino';
	public $link = 'http://www.molinorestaurant.cz/';
	public $icon = 'italy';


	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = cache_get_html($this->title, $this->link, $cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$header = $cached['html']->find("#denni-menu h2");
		$table = $cached['html']->find("#denni-menu table");

		if (!$header) {
			throw new ScrapingFailedException("H2 not found");
		}

		if (!$table) {
			throw new ScrapingFailedException("Table not found");
		}

		$today_re = '(' . get_czech_day(date('w', $todayDate)) . '\s+' . date('j', $todayDate). '\.\s*' . date('n', $todayDate) . ')ui';

		$withinToday = FALSE;
		foreach ($table[0]->find('tr') as $tr) {
			$tds = $tr->find('td');
			$what = trim(preg_replace('~\x{00a0}~siu',' ', $tds[0]->plaintext));
			if (!$what) {
				continue;
			} elseif ($withinToday) {
				if (count($tds) == 1) {
					break;
				}
				$quantity = empty($tds[1])? '' : $tds[1]->plaintext;
				$price = empty($tds[2])? '' : $tds[2]->plaintext;
				$result->dishes[] = new Dish($what, $price, $quantity);
			} elseif (count($tds) == 1) {
				if (preg_match($today_re, $what)) {
					$withinToday = TRUE;
				}
			}
		}

		return $result;
	}

/*
	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = cache_get_html($this->title, $this->link, $cacheSourceExpires);

		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}

		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$h1 = $cached['html']->find("#category-content .article-content h1");
		$table = $cached['html']->find("#category-content .article-content table");

		if (!$h1) {
			throw new ScrapingFailedException("H1 not found");
		}

		$group = $h1[0]->plaintext;
		foreach ($table[0]->find('tr') as $tr) {
			$tds = $tr->find('td');
			$what = implode('', $tds[0]->find('text'));
			$quantity = implode('', $tds[1]->find('text'));
			$price = implode('', $tds[2]->find('text'));
			$result->dishes[] = new Dish($what, $price, $quantity, $group);
		}

		return $result;
	}
*/
}
