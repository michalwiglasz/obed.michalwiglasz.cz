<?php

date_default_timezone_set('Europe/Prague');

require_once __DIR__ . '/string.php';
require_once __DIR__ . '/simple_html_dom.php';
require_once __DIR__ . '/pdf2text.php';

define('CACHE_DIR', __DIR__ . '/cache');

// load modules
foreach(glob(__DIR__ . '/modules/*.php') as $module) {
	require_once $module;
}


function get_http_headers() {
	return implode("\r\n", array(
		"User-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36",
		"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
	)) . "\r\n";
}


function get_today_timestamp() {
	static $today_timestamp = NULL;
	if (!$today_timestamp) $today_timestamp = time();
	return $today_timestamp;
}


function get_czech_day($daynum) {
	static $days = array(
		'neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota', 'neděle'
	);
	return $days[$daynum];
}


function print_infobox() {
	//echo '<p class="infobox">Zdá se, že Zomato nás zablokovalo na firewallu... 😞</p>';
}


function escape_text($str)
{
	return htmlspecialchars(strip_tags(trim($str)));
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
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />

<meta http-equiv="refresh" content="3600">
<meta property="og:title" content="Jíííídlooooo">
<meta property="og:description" content="' . escape_text($description) . '">
<meta property="og:url" content="' . $root . '">
<meta property="og:image" content="/GxMLDqy.gif">

<meta name="twitter:card" value="summary_large_image">
<meta name="twitter:domain" value="obed.michalwiglasz.cz">
<meta name="twitter:title" value="Jíííídlooooo">
<meta name="twitter:description" value="' . escape_text($description) . '">
<meta name="twitter:url" value="' . $root . '">
<meta name="twitter:image" value="/GxMLDqy.gif">

<!-- Global Site Tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-31464798-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments)};
  gtag("js", new Date());

  gtag("config", "UA-31464798-2");
</script>

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/8c02b2c92d.js"></script>
<script src="/script.js?' . filemtime(__DIR__ . '/script.js') . '"></script>

<title>Jíííídlooooo</title>
<link rel="shortcut icon" href="/favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic" rel="stylesheet" type="text/css">
<link href="/style.css?' . filemtime(__DIR__ . '/style.css') . '" rel="stylesheet" type="text/css">
	';
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

function make_cache_dir() {
	@mkdir(CACHE_DIR, 770, TRUE);
}

function cache_file($key) {
	return CACHE_DIR . '/' . webalize($key) . '.cache';
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

	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36');
	$response = curl_exec($ch);
	curl_close($ch);

	return cache_store($key, [
		'contents' => $response,
	]);
}

function cache_get_html($key, $url, $expires=540) {
	$key = 'get-html-' . $key;
	$cached = cache_retrieve($key, $expires);
	if ($cached) return $cached;

	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36');
	$response = curl_exec($ch);
	curl_close($ch);

	$html = str_get_html($response);
	return cache_store($key, [
		'html' => $html,
	]);
}

function print_header($restaurant)
{
	echo "\t\t";
	if ($restaurant->icon) {
		$id = 'r-' . md5(spl_object_hash($restaurant));
		echo "<h1 id=\"$id\" class=\"emoji $restaurant->icon\">";
		echo "<style>h1#$id.emoji.$restaurant->icon:after { background-image: url('/em-$restaurant->icon.png'); }</style>";
	}
	else echo '<h1>';
	echo escape_text($restaurant->title) . "</h1>\n";
	echo "\t\t" . '<p class="retrieved">Aktualizováno ' . date('j. n. Y H:i:s', $restaurant->error? time() : $restaurant->timestamp);
	if ($restaurant->link) echo ' &mdash; <a href="'.escape_text($restaurant->link) . '">web</a>';
	if ($restaurant->sourceLink) echo ' &mdash; <a href="'.escape_text($restaurant->sourceLink) . '">zdroj</a>';
	if ($restaurant->note) echo ' &mdash; ' . escape_text($restaurant->note);
	echo '</p>' . "\n";
}

function print_footer() {
	echo "\n\t\t<hr>";
	echo "\n\t\t" . '<p class="footer">Základy této stránky vytvořil <a href="http://www.fit.vutbr.cz/~igrochol/">David Grochol</a> během jednoho nudného víkendu (a rozhodně ne během své pracovní doby). <a href="https://michalwiglasz.cz">Michal Wiglasz</a> ji upravil, aby vypadala trochu k světu a nenačítala se půl dne, a propůjčil hosting a doménu. Máme i <a href="?json">výstup v JSONu</a> pro strojové zpracování a <a href="https://github.com/michalwiglasz/obed.michalwiglasz.cz">GitHub</a>, kam můžete psát připomínky a posílat patche.</p>' . "\n";
}

function print_subheader($title)
{
	echo "\t\t<h2>" . escape_text($title) . "</h2>\n";
}

function print_dishes_prologue()
{
	echo "\t\t<ul>\n";
}

function print_dishes_epilogue()
{
	echo "\t\t</ul>\n";
}

function print_dish($dish)
{
	echo "\t\t\t<li>\n";
	if ($dish->number) {
		echo "\t\t\t\t" . '<span class="number">' . escape_text($dish->number) . '.</span>' . "\n";
	}
	if ($dish->quantity) {
		echo "\t\t\t\t" . '<span class="quantity">' . escape_text($dish->quantity) . '</span>' . "\n";
	}
	if ($dish->name) {
		echo "\t\t\t\t" . '<span class="name">' . escape_text($dish->name) . '</span>' . "\n";
	}
	if (is_array($dish->price)) {
		echo "\t\t\t\t" . '<span class="hellip">&hellip;</span>' . "\n";
		$first = true;
		foreach ($dish->price as $key => $price) {
			$webalized_key = escape_text(webalize($key));
			if (!$first) {
				echo "\t\t\t\t" . '<span class="slash">/</span>' . "\n";
			}
			$first = false;
			echo "\t\t\t\t" . '<span class="price price-' . $webalized_key . '" title="' . escape_text($key) . '">' . escape_text($price) . '</span>' . "\n";
		}
	} elseif ($dish->price) {
		echo "\t\t\t\t" . '<span class="hellip">&hellip;</span>' . "\n";
		echo "\t\t\t\t" . '<span class="price">' . escape_text($dish->price) . '</span>' . "\n";
	}
	echo "\t\t\t</li>\n";
}

function print_error($what)
{
	print_dishes_prologue();
	echo "\t\t\t\t" . '<span class="error">' . escape_text($what) . '</span>' . "\n";
	print_dishes_epilogue();
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
	make_cache_dir();
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
			'sourceLink' => $module->sourceLink,
			'note' => $module->note,
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
		print_header($restaurant);

		if ($restaurant->error) {
			print_error('Nepodařilo se načíst menu.');

		} else {
			if (count($restaurant->dishes)) {
				$grouped = group_dishes($restaurant->dishes);
				foreach ($grouped as $name => $items) {
					if ($name) print_subheader($name);
					print_dishes_prologue();
					foreach ($items as $dish) {
						print_dish($dish);
					}
					print_dishes_epilogue();
				}
			} else {
				print_error('Vypadá to, že dnes nic.');
			}
		}
	}
}
