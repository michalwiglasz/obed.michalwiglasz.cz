<?php

class Menza extends LunchMenuSource {

	public $title = 'Menza';
	public $link = 'http://www.kam.vut.cz/?p=menu&provoz=5';
	public $icon = 'ambulance';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadHtml($cacheSourceExpires);
		$result = new LunchMenuResult($cached['stored']);
		$group = null;

		$table = $cached['html']->find("table#m5", 0);

		if (!$table) {
			throw new ScrapingFailedException("table#m5 was not found");
		}

		$mapping = array(
			"td.levy" => "type",
			"td.levyjid" => "name",
			"td.slcen1" => "priceStudent",
			"td.slcen2" => "priceEmployee",
			"td.slcen3" => "priceExternal",
		);

		foreach ($table->find("tr") as $i => $row) {

			$values = array_fill_keys(array_keys($mapping), 0);
			foreach ($mapping as $selector => $key) {
				$element = $row->find($selector);
				if ($element) {
					$value = $element[0]->plaintext;
					$value = preg_replace('/&nbsp;/i', ' ', $value);
					$values[$key] = $value;
				}
			}

			if (!$values["name"]) {
				continue;
			}

			$type = substr($values["type"], 0, 1);
			if ($type === "H") {
				$group = "Hlavní jídlo";
			} elseif ($type === "P") {
				$group = "Polévka";
			} else {
				$group = "Ostatní";
			}

			$price = array(
				"Student" => $values["priceStudent"],
				"Zaměstnanec" => $values["priceEmployee"],
				"Externí stravník" => $values["priceExternal"],
			);

			$result->dishes[] = new Dish($values["name"], $price, null, $group);
		}

		return $result;

	}
}
