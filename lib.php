<?php

ini_set('display_errors', 'off');
date_default_timezone_set('Europe/Prague');

require_once __DIR__ . '/string.php';
require_once __DIR__ . '/simple_html_dom.php';

// load modules
foreach(glob(__DIR__ . '/modules/*.php') as $module) {
	require_once $module;
}


function get_today_timestamp() {
	static $today_timestamp = NULL;
	if (!$today_timestamp) $today_timestamp = time();
	return $today_timestamp;
}


function print_infobox() {
	//echo '<p class="infobox">Zdá se, že Zomato nás zablokovalo na firewallu... 😞</p>';
}

function print_html_head($root, $description='Denní menu restaurací v okolí') {
	echo '<!DOCTYPE html><!--
      ▄▄▄▄· ▄▄▄ .·▄▄▄▄     • ▌ ▄ ·. ▪   ▄▄·  ▄ .▄ ▄▄▄· ▄▄▌  ▄▄▌ ▐ ▄▌▪   ▄▄ • ▄▄▌   ▄▄▄· .▄▄ · ·▄▄▄▄•    ▄▄· ·▄▄▄▄•
▪     ▐█ ▀█▪▀▄.▀·██▪ ██    ·██ ▐███▪██ ▐█ ▌▪██▪▐█▐█ ▀█ ██•  ██· █▌▐███ ▐█ ▀ ▪██•  ▐█ ▀█ ▐█ ▀. ▪▀·.█▌   ▐█ ▌▪▪▀·.█▌
 ▄█▀▄ ▐█▀▀█▄▐▀▀▪▄▐█· ▐█▌   ▐█ ▌▐▌▐█·▐█·██ ▄▄██▀▐█▄█▀▀█ ██▪  ██▪▐█▐▐▌▐█·▄█ ▀█▄██▪  ▄█▀▀█ ▄▀▀▀█▄▄█▀▀▀•   ██ ▄▄▄█▀▀▀•
▐█▌.▐▌██▄▪▐█▐█▄▄▌██. ██    ██ ██▌▐█▌▐█▌▐███▌██▌▐▀▐█ ▪▐▌▐█▌▐▌▐█▌██▐█▌▐█▌▐█▄▪▐█▐█▌▐▌▐█ ▪▐▌▐█▄▪▐██▌▪▄█▀   ▐███▌█▌▪▄█▀
 ▀█▄▀▪·▀▀▀▀  ▀▀▀ ▀▀▀▀▀•  ▀ ▀▀  █▪▀▀▀▀▀▀·▀▀▀ ▀▀▀ · ▀  ▀ .▀▀▀  ▀▀▀▀ ▀▪▀▀▀·▀▀▀▀ .▀▀▀  ▀  ▀  ▀▀▀▀ ·▀▀▀ • ▀ ·▀▀▀ ·▀▀▀ •
-->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width">

<meta http-equiv="refresh" content="3600">
<meta property="og:title" content="Jíííídlooooo">
<meta property="og:description" content="' . htmlspecialchars($description) . '">
<meta property="og:url" content="' . $root . '">
<meta property="og:image" content="/GxMLDqy.gif">

<meta name="twitter:card" value="summary_large_image">
<meta name="twitter:domain" value="obed.michalwiglasz.cz">
<meta name="twitter:title" value="Jíííídlooooo">
<meta name="twitter:description" value="' . htmlspecialchars($description) . '">
<meta name="twitter:url" value="' . $root . '">
<meta name="twitter:image" value="/GxMLDqy.gif">

<title>Jíííídlooooo</title>
<link rel="shortcut icon" href="/favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic" rel="stylesheet" type="text/css">
<link href="/style.css" rel="stylesheet" type="text/css">
<script src="/script.js"></script>
	';
}

function print_footer() {
	echo '<hr><p class="footer">Základy této stránky vytvořil <a href="http://www.fit.vutbr.cz/~igrochol/">David Grochol</a> během jednoho nudného víkendu (a rozhodně ne během své pracovní doby). <a href="https://michalwiglasz.cz">Michal Wiglasz</a> ji upravil, aby vypadala trochu k světu a nenačítala se půl dne, a propůjčil hosting a doménu. Máme i <a href="?json">výstup v JSONu</a> pro strojové zpracování a <a href="https://github.com/michalwiglasz/obed.michalwiglasz.cz">GitHub</a>, kam můžete psát připomínky a posílat patche.</p>';
}

function dump($obj) {
	echo "<pre><code>";
	var_dump($obj);
	echo "</code></pre>";
	return $obj;
}

function startswith($str, $prefix) {
	return substr($str, 0, strlen($prefix)) == $prefix;
}

function filter_output($filters, $element) {
	$str = (string)$element;
	foreach($filters as $regex => $repl) {
		$str = preg_replace($regex, $repl, $str);
	}
	return $str;
}

function cache_file($key) {
	return __DIR__ . '/cache/' . webalize($key) . '.cache';
}

function cache_retrieve($key, $expires=600) {
	$cached = @file_get_contents(cache_file($key));
	if ($cached) {
		$cached = unserialize($cached);
		if ($cached['stored'] > time() - $expires) {
			return $cached;
		}
	}
}

function cache_store($key, $data) {
	$data['stored'] = time();
	file_put_contents(cache_file($key), serialize($data));
	return $data;
}

function cache_html_start($key, $expires=600) {
	$key = "html-" . $key;
	if ($c = cache_retrieve($key, $expires)) {
		echo $c['html'];
		return true;
	}

	ob_start();
	return false;
}

function cache_html_end($key) {
	$key = "html-" . $key;
	$html = ob_get_contents();
	ob_end_flush();
	cache_store($key, [
		'html' => $html
	]);
}

function cache_download($key, $url, $expires=540) {
	$key = 'download-' . $key;
	$cached = cache_retrieve($key, $expires);
	if ($cached) return $cached;

	$opts = array(
		'http'=>array(
		'method'=>"GET",
		'header'=>
			"User-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36\r\n"
		)
	);
	$context = stream_context_create($opts);
	$data = file_get_contents($url, null, $context);

	return cache_store($key, [
		'contents' => $data,
	]);
}

function cache_get_html($key, $url, $expires=540, $fulluri = true) {
	$key = 'get-html-' . $key;
	$cached = cache_retrieve($key, $expires);
	if ($cached) return $cached;

	$sniServer = parse_url($url, PHP_URL_HOST);
	$opts = array(
		'http' => array(
			'method' => "GET",
			'header' => "User-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36\r\n",
			'timeout' => 2,
			//'proxy' => 'tcp://155.4.66.102:45554',
			'request_fulluri' => $fulluri,
		),
		'ssl' => array(
			'SNI_enabled' => true,
			'SNI_server_name' => $sniServer,
		)
	);
	$context = stream_context_create($opts);
	$data = file_get_html($url, null, $context);

	return cache_store($key, [
		'html' => $data,
	]);
}

function process_zomato($zomato, $cache_default_interval, $cache_html_interval, $filters=[])
{
	foreach ($zomato as $title => $vals) {
		if (count($vals) == 3) {
			list($scrape, $link, $emoji) = $vals;
			$zomato_id = NULL;
		} else {
			list($scrape, $link, $emoji, $zomato_id) = $vals;
		}

		if (cache_html_start($title, $cache_default_interval)) {
			continue; // cache hit
		}

		if ($zomato_id) {
			$cached = zomato_api_download($title, "https://developers.zomato.com/api/v2.1/dailymenu?res_id=$zomato_id", $cache_html_interval);
		} else {
			$cached = cache_get_html($title, $scrape, $cache_html_interval);
		}
		print_header($title, $link, $emoji, $cached['stored']);

		do {
			if ($cached['html']) {
				$menu = $cached['html']->find("#menu-preview div.tmi-group", 0);
				if ($menu) {
					echo filter_output($filters, $menu);
					break;
				}

			} elseif ($cached['contents']) {
				if (count($cached['contents']->daily_menus)) {
					foreach ($cached['contents']->daily_menus[0]->daily_menu->dishes as $dish) {
						$what = $dish->dish->name;
						$price = $dish->dish->price;
						$quantity = NULL;
						print_item($what, $price, $quantity);
					}
				} else {
					echo "Nemají menu na Zomatu.";
				}
				break;
			}

			echo "Nepovedlo se načíst menu ze Zomata.";

		} while (FALSE);

		cache_html_end($title);
	}
}

function print_header($title, $link, $emoji, $retrieved, $note=NULL)
{
	if ($emoji) echo '<h1 class="emoji ' . $emoji . '">';
	else echo '<h1>';
	echo '<a href="'.htmlspecialchars($link) . '">' . htmlspecialchars($title) . '</a></h1>';
	echo '<p class="retrieved">Aktualizováno ' . date('j. n. Y H:i:s', $retrieved);
	echo ' &mdash; <a href="'.htmlspecialchars($link) . '">web</a></h1>';
	if ($note) echo ' &mdash; ' . htmlspecialchars($note);
	echo '</p>';
}

function print_subheader($title)
{
	echo '<div class="tmi-group-name">' . $title . '</div>';
}

function print_what($what, $quantity = NULL)
{
	echo '<div class="tmi-text-group col-l-14"><div class="row"><div class="tmi-name">';
	if ($quantity) echo '<span class="tmi-qty">' . htmlspecialchars(strip_tags($quantity)) . ' </span>';
	echo htmlspecialchars(strip_tags($what));
	echo "\n" . '</div></div></div>';
}

function print_price($price)
{
	echo '<div class="tmi-price ta-right col-l-2 bold"><div class="row">' . "\n";
	echo htmlspecialchars(strip_tags($price));
	echo '</div></div>';
}

function print_item($what, $price = NULL, $quantity = NULL)
{
	echo '<div class="tmi tmi-daily pb5 pt5">';
	if ($what) print_what($what, $quantity);
	if ($price) print_price($price);
	echo '</div>';
}

function group_dishes($menu)
{
	$grouped = [
		'' => [],
	];
	foreach ($menu as $dish) {
		if ($dish->group) {
			if (isset($grouped[$dish->group])) {
				$grouped[$dish->group][] = $dish;
			} else {
				$grouped[$dish->group] = [$dish];
			}

		} else {
			$grouped[''][] = $dish;
		}
	}

	return $grouped;
}

function collect_menus($sources, $cache_default_interval)
{
	$menus = [];
	foreach ($sources as $source) {

		$module = $source->module;
		$expires = $source->cacheExpires? $source->cacheExpires : $cache_default_interval;

		try {
			$dishes = $module->getTodaysMenu(get_today_timestamp(), $expires);
			$error = NULL;

		} catch (ScrapingFailedException $ex) {
			$dishes = new LunchMenuResult(time());
			$error = $ex->getMessage();
		}

		$menus[webalize($module->title)] = (object)[
			'title' => $module->title,
			'link' => $module->link,
			'icon' => $module->icon,
			'error' => $error,
			'timestamp' => $dishes->timestamp,
			'dishes' => $dishes->dishes,
		];
	}

	return $menus;
}

function print_json($root, $menus)
{
	$json = [
		'source' => $root,
		'authors' => 'David Grochol, Michal Wiglasz',
		'restaurants' => [],
	];

	foreach ($menus as $key => $value) {
		$json['restaurants'][$key] = $value;
		$json['restaurants'][$key]->timestamp = date('c', $json['restaurants'][$key]->timestamp);
	}

	header('content-type: application/json; charset=utf-8');
	echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}


function print_html($root, $menus)
{
	foreach ($menus as $restaurant) {
		if ($restaurant->error) {
			print_header($restaurant->title, $restaurant->link, $restaurant->icon, time());
			print_item('Nepodařilo se načíst menu.');

		} else {
			print_header($restaurant->title, $restaurant->link, $restaurant->icon, $restaurant->timestamp);
			if (count($restaurant->dishes)) {
				$grouped = group_dishes($restaurant->dishes);
				foreach ($grouped as $name => $items) {
					if ($name) print_subheader($name);
					foreach ($items as $dish) {
						print_item($dish->name, $dish->price, $dish->quantity);
					}
				}
			} else {
				print_item('Dnes nemají nic.');
			}
		}
	}
}
