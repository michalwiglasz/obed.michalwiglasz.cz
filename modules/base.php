<?php

abstract class LunchMenuSource
{
	public $title;
	public $link;
	public $sourceLink;
	public $icon;
	public $note;

	abstract public function getTodaysMenu($todayDate, $cacheSourceExpires);

	protected function downloadHtml($cacheSourceExpires, $url = null)
	{
		if (!$url) {
			$url = $this->sourceLink? $this->sourceLink : $this->link;
		}
		$cached = cache_get_html($this->title, $url, $cacheSourceExpires);
		if (!$cached['html']) {
			throw new ScrapingFailedException("No html returned");
		}
		return $cached;
	}

	protected function downloadRaw($cacheSourceExpires)
	{
		$url = $this->sourceLink? $this->sourceLink : $this->link;
		$cached = cache_download($this->title, $url, $cacheSourceExpires);
		if (!$cached['contents']) {
			throw new ScrapingFailedException("File empty");
		}
		return $cached;
	}
}


class LunchMenuResult
{
	public $timestamp;
	public $dishes;

	public function __construct($timestamp, $dishes = array())
	{
		$this->timestamp = $timestamp;
		$this->dishes = $dishes;
	}
}


class Source
{
	public $cacheExpires;
	public $module;

	public function __construct($module, $cacheExpires=NULL)
	{
		$this->module = $module;
		$this->cacheExpires = $cacheExpires;
	}
}


class Dish
{
	public $number;
	public $name;
	public $price;
	public $quantity;
	public $group;

	public function __construct($name, $price=NULL, $quantity=NULL, $group=NULL, $number=NULL)
	{
		$this->name = trim($name);
		if (is_array($price)) {
			$this->price = $price;
		} else {
			$this->price = trim($price);
		}
		$this->quantity = trim($quantity);
		$this->group = trim($group);
		$this->number = trim($number);

		// try to extract number from name
		if (is_null($number)) {
			if (preg_match('(^(?:menu\s+(?:č\.\s+)?)?([0-9]+)[.:]\s*(.+))ui', $this->name, $m)) {
				$this->number = trim($m[1]);
				$this->name = trim($m[2]);
			}
		}

		// try to extract quantity from name
		if (is_null($quantity)) {
			if (preg_match('(^([0-9]+\.)?\s*([0-9,.]+)\s*([gl])\s+(.+?)$)ui', $this->name, $m)) {
				// found at the beginning
				$this->name = trim("$m[1] $m[4]");
				$this->quantity = trim("$m[2] $m[3]");

			} elseif (preg_match('(([0-9]+\.)?\s*(.+?)([0-9,.]+)\s*([gl])$)ui', $this->name, $m)) {
				// found at the end
				$this->name = trim("$m[1] $m[2]");
				$quantity = trim("$m[3] $m[4]");
			}

		} else {
			// try to fix spacing
			if (preg_match('(^([0-9,.]+)\s*([gl])$)ui', $this->quantity, $m)) {
				$this->quantity = trim("$m[1] $m[2]");
			}
		}
	}
}


class ScrapingFailedException extends Exception
{

}
