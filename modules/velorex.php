<?php

class Velorex extends LunchMenuSource
{
	public $title = 'Velorex';
	public $link = 'http://www.restauracevelorex.cz/';
	public $icon = 'velorex';


	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$header = $cached['html']->find("#denniNabidka p.underline");
		$nabidka = $cached['html']->find("#denniNabidka");

		if (!$header) {
			throw new ScrapingFailedException("Header not found");
		}

		foreach($nabidka[0]->find('p.bold, table') as $el) {
			if ($el->tag == 'p') {
				$group = $el->plaintext;
			} else {
				foreach ($el->find('tr') as $tr) {
					$tds = $tr->find('td');

					if (count($tds) == 4) {
						$what = $tds[0]->plaintext . ' ' . $tds[2]->plaintext;
						$price = $tds[3]->plaintext;
					} elseif (count($tds) == 3) {
						$what = $tds[1]->plaintext;
						$price = $tds[2]->plaintext;
					} else {
						$what = $tds[1]->plaintext;
						$price = NULL;
					}

					$what = str_replace('&nbsp;', ' ', $what);
					$price = str_replace('&nbsp;', ' ', $price);

					$quantity = NULL;
					$result->dishes[] = new Dish($what, $price, $quantity, $group);
				}
			}
		}

		return $result;
	}
}
