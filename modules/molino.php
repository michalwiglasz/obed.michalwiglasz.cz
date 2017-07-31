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

		$group = $header[0]->plaintext;
		foreach ($table[0]->find('tr') as $tr) {
			$tds = $tr->find('td');
			$what = implode('', $tds[0]->find('text'));
			$quantity = empty($tds[1])? '' : implode('', $tds[1]->find('text'));
			$price = empty($tds[2])? '' : implode('', $tds[2]->find('text'));
			$result->dishes[] = new Dish($what, $price, $quantity, $group);
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
