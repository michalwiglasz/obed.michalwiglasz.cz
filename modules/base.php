<?php

abstract class LunchMenuSource
{
	public $title;
	public $link;
	public $icon;

	abstract public function getTodaysMenu($todayDate, $cacheSourceExpires);
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
	public $name;
	public $price;
	public $quantity;
	public $group;

	public function __construct($name, $price=NULL, $quantity=NULL, $group=NULL)
	{
		$this->name = $name;
		$this->price = $price;
		$this->quantity = $quantity;
		$this->group = $group;
	}
}


class ScrapingFailedException extends Exception
{

}
