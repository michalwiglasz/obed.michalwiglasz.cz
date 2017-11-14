<?php

class Zomato extends LunchMenuSource
{
	private $id;

	public function __construct($id, $title, $link, $icon)
	{
		$this->id = $id;
		$this->title = $title;
		$this->link = $link;
		$this->icon = $icon;

		$restaurant = $this->apiDownload($this->title . '-info', "https://developers.zomato.com/api/v2.1/restaurant?res_id=$this->id", 86400 * 7);
		$this->sourceLink = $restaurant['contents']->url;
	}

	private function apiDownload($key, $url, $expires) {
		$key = 'zomato-' . $key;
		$cached = cache_retrieve($key, $expires);
		if ($cached) return $cached;

		$opts = array(
			'http'=>array(
			'method'=>"GET",
			'header'=>
				"User-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36\r\n" .
				"user_key: 49cc6260f86619dad16b69558f43f4be\r\n"
			)
		);
		$context = stream_context_create($opts);
		$data = file_get_contents($url, null, $context);

		return cache_store($key, [
			'contents' => json_decode($data),
		]);
	}

	public function getTodaysMenu($todayDate, $cacheSourceExpires)
	{
		$cached = $this->apiDownload($this->title . '-menu', "https://developers.zomato.com/api/v2.1/dailymenu?res_id=$this->id", $cacheSourceExpires);

		if (!$cached['contents']) {
			throw new ScrapingFailedException("No data returned from api");
		}

		$result = new LunchMenuResult($cached['stored']);

		if (count($cached['contents']->daily_menus)) {
			foreach ($cached['contents']->daily_menus[0]->daily_menu->dishes as $dish) {
				$what = $dish->dish->name;
				$price = $dish->dish->price;
				$quantity = NULL;
				$result->dishes[] = new Dish($what, $price);
			}
		}

		return $result;
	}
}
