<?php

class Kralovska extends LunchMenuSource
{
	public $title = 'Královská cesta';
	public $link = 'http://www.kralovskacesta.com/';
	public $icon = 'kralovska';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);
		$group = NULL;

		$ul = $cached['html']->find("ul.denni-jidlo-hp", 0);
		if (!$ul) {
			throw new ScrapingFailedException("ul.denni-jidlo-hp not found");
		}

		foreach ($ul->find('li') as $i => $li) {
			if ($i == 0) {
				$what = trim($li->plaintext);
				$price = NULL;
			} else {
				$what = trim($li->find('span.nazev-jidla', 0)->plaintext);
				$price = trim($li->find('span.cena-jidla', 0)->plaintext);
			}

			if (!$what) continue;
			$result->dishes[] = new Dish($what, $price, NULL, $group);
		}

		return $result;
	}
}
