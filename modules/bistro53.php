<?php

class Bistro53 extends LunchMenuSource
{
	public $title = 'Bistro 53';
	public $link = 'https://goo.gl/maps/Z9WC3GeaRJjUDk136';
	public $sourceLink = 'https://goo.gl/maps/o4ggsLYfQwhRimAo8';
	public $icon = 'bowl';

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$result = new LunchMenuResult(DateTime::createFromFormat(DateTime::ATOM, "2019-05-31T19:30:00+0200")->getTimestamp());

		$result->dishes[] = new Dish("Tofu se zeleninou (s přílohou)", "99 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Bún bò Nam Bộ chay (tofu, zelenina, bylinky, zálivka)", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Bún bò Nam Bộ (rýžové nudle, zálivka, salát, zelenina, koriandr, restované hovězí maso, arašídy, sušená cibule)", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Bún chả (rýžové nudle, zálivka, salát, zelenina, koriandr, smažené závitky, asijské masové karbanátky)", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Nem rán – smažené asijské závitky (mleté vepřové maso, skleněné nudle, jidášovo ucho, zelenina)", "45 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Bún xào (restované rýžové nudle, kuřecí/vepřové maso, zelenina)", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Phở xào (ploché rýžové nudle, kuřecí/vepřové maso, zelenina)", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Phở bò (hovězí vývar, hovězí maso, ploché rýžové nudle, koriandr a bylinky)", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Phở gà (hovězí vývar, kuřecí maso, ploché rýžové nudle, koriandr a bylinky)", "115 Kč", NULL, NULL);

		$result->dishes[] = new Dish("Hovězí na zelenině / po thajsku", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Krevety na zelenině / po thajsku", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Kuřecí na zelenině / po thajsku", "115 Kč", NULL, NULL);

		$result->dishes[] = new Dish("Pečené kuře s přílohou", "115 Kč", NULL, NULL);
		$result->dishes[] = new Dish("Pečená kachna s přílohou", "129 Kč", NULL, NULL);

		return $result;
	}
}
