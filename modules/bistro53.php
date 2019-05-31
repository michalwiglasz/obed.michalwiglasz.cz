<?php

class Bistro53 extends LunchMenuSource
{
	public $title = 'Bistro 53';
	public $link = '';
	public $icon = '';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
	
		$result->dishes[] = new Dish("Menu jeste neni kompletni...", "", NULL, "Poznamka");
		$result->dishes[] = new Dish("Pokud poslete fitku menu, doplnime :)", "", NULL, "Poznamka");

		$result->dishes[] = new Dish("PHO GA", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("PHO BO", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("BUN BO NAM BO", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("BUN CHA NEM", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("BUN XAO", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("Pecena Kachna (s prilohou)", "???", NULL, NULL);
		$result->dishes[] = new Dish("Pecene Kure (s prilohou)", "???", NULL, NULL);
		$result->dishes[] = new Dish("NEM RAN (zavitky)", "45Kc", NULL, NULL);
		$result->dishes[] = new Dish("BUN BO NAM BO CHAY", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("PHO XAO", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("TOFU SE ZELENINOU", "99Kc", NULL, NULL);
		$result->dishes[] = new Dish("Kure na zelenine po thajsku", "99Kc", NULL, NULL);
		$result->dishes[] = new Dish("Hovezi na zelenine po thajsku", "115Kc", NULL, NULL);
		$result->dishes[] = new Dish("krevety na zelenine po thajsku", "125Kc", NULL, NULL);
		
		
		return $result;
	}
}
