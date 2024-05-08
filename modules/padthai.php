<?php

class PadThai extends LunchMenuSource
{
	public $title = 'Pad Thai';
	public $link = 'http://padthairestaurace.cz/';
	public $sourceLink = 'http://padthairestaurace.cz/menu/menu.pdf';
	public $icon = 'thailand';
	public $note = 'Silně experimentální, menu se tahá z PDF, lepší zdroj bohužel není :-(';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->downloadRaw($cacheSourceExpires);
		$today = date('N', $todayDate) - 1;  // 0 = monday, 6 = sunday
		$result = new LunchMenuResult($cached['stored']);

		$pdf = new PDF2Text();
		$pdf->setContents($cached['contents']);
		$pdf->decodePDF();

		$output = $pdf->output();
		$output = preg_replace('![ ]+!', ' ', $output); # reduce spaces
		$output = preg_replace('![\n]+!', '', $output); # remove newlines

		$substrs = preg_split('/\s*(Pondělí|Úterý|Středa|ýtvrtek|Pátek|Seznam alergenů)\s*/', $output);
		$weekmenu = array_slice($substrs, 1, 5);
		$daymenu = $weekmenu[$today];

		$match = preg_match('/^(Polévka[:]\s+.*)\s+(1\..*)\s+(2\..*)\s+(3\..*)/', $daymenu, $matches);
		$dishes = array_slice($matches, 1);

		foreach($dishes as $dish) {
			$match = preg_match('/^\s*(.*?)\s+([0-9]*\s*Kč)\s*$/', $dish, $matches);
			if ($match) {
				$what = $matches[1];
				$price = $matches[2];
				$result->dishes[] = new Dish($what, $price);
			} else {
				$result->dishes[] = new Dish($dish);
			}
		}

		return $result;
	}
}
