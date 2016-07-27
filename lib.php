<?php

require_once dirname(__FILE__) . '/string.php';
require_once dirname(__FILE__) . '/simple_html_dom.php';

function dump($obj) {
	echo "<pre><code>";
	ob_start();
	var_dump($obj);
	$dump = ob_get_contents();
	ob_end_clean();
	echo htmlspecialchars($dump);
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
	return 'cache-' . webalize($key) . '.cache';
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

function cache_get_html($key, $url, $expires=540) {
	$key = 'get-html-' . $key;
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
	$data = file_get_html($url, null, $context);

	return cache_store($key, [
		'html' => $data,
	]);
}

function process_zomato($zomato, $cache_default_interval, $cache_html_interval, $filters=[])
{
	foreach ($zomato as $title => $vals) {
		list($scrape, $link, $emoji) = $vals;
		if (cache_html_start($title, $cache_default_interval)) {
			continue; // cache hit
		}

		$cached = cache_get_html($title, $scrape, $cache_html_interval);
		print_header($title, $link, $emoji, $cached['stored']);

		$menu = $cached['html']->getElementById("menu-preview");
		if ($menu) {
			foreach ($menu->getElementsByTagName("div") as $element) {
				$classy = $element->getAttribute("class");
				if ($element->getAttribute("class") == "tmi-group") {
					foreach ($element->children as $child) {
						if (strpos($child->getAttribute("class"), "tmi-group-name") !== FALSE) {
							print_subheader($child->plaintext);
						} else {
							$what = $child->find('.tmi-name', 0)->plaintext;
							$price = $child->find('.tmi-price', 0)->plaintext;
							print_item($what, $price);
						}
					}
					break;
				}

			}
		} else {
			echo "Nepovedlo se načíst menu ze Zomata.";
		}

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
	$what = preg_replace('(\\((A.)?[0-9,\\s]+\\))i', '', $what);
	$what = htmlspecialchars(strip_tags($what));

	if (is_null($quantity)) {
		// auto-mark quantities
		$what = preg_replace_callback('((([0-9][,.])?[0-9]+\\s*([gl]|ks)))', function ($m) {
			return '<span class="tmi-qty">' . $m[0] . ' </span>';
		}, $what);
	}

	echo '<span class="tmi-name">';
	if ($quantity) echo '<span class="tmi-qty">' . htmlspecialchars(strip_tags($quantity)) . ' </span>';
	echo $what;  // sanitized above
	echo "\n" . '</span>';
}

function print_price($price)
{
	echo '<span class="tmi-price">' . "\n";
	echo htmlspecialchars(strip_tags($price));
	echo '</span>';
}

function print_item($what, $price = NULL, $quantity = NULL)
{
	echo '<div class="tmi-daily">';
	if ($what) print_what($what, $quantity);
	if ($price) print_price($price);
	echo '</div>';
}
