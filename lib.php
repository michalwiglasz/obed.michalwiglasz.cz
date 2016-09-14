<?php

require_once dirname(__FILE__) . '/string.php';
require_once dirname(__FILE__) . '/simple_html_dom.php';

require_once dirname(__FILE__) . '/vendor/autoload.php';
use Zend\Text\Figlet\Figlet;
$figlet = new Figlet();
$figlet->setOutputWidth(500);
$figlet->setFont(dirname(__FILE__) . '/figletfonts/big.flf');
//$figlet->setSmushMode(Figlet::SM_KERN);

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
	$source = file_get_contents($url, null, $context);
	$data = str_get_html($source);

	return cache_store($key, [
		'source' => $source,
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
				if (strcasecmp($classy, "tmi-group") == 0) {
					echo filter_output($filters, $element);
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
	global $figlet;
	$asciiart = str_unczech($title);
	$figlet_text = $figlet->render($asciiart);
	echo "\n<!--\n";
	//echo "\n<pre>\n";
	echo $figlet_text;
	echo "-->\n";
	//echo "</pre>\n";

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

function str_unczech($str) {
	static $table = [
		'ä'=>'a', 'Ä'=>'A', 'á'=>'a', 'Á'=>'A', 'à'=>'a', 'À'=>'A', 'ã'=>'a',
		'Ã'=>'A', 'â'=>'a', 'Â'=>'A', 'č'=>'c', 'Č'=>'C', 'ć'=>'c', 'Ć'=>'C',
		'ď'=>'d', 'Ď'=>'D', 'ě'=>'e', 'Ě'=>'E', 'é'=>'e', 'É'=>'E', 'ë'=>'e',
		'Ë'=>'E', 'è'=>'e', 'È'=>'E', 'ê'=>'e', 'Ê'=>'E', 'í'=>'i', 'Í'=>'I',
		'ï'=>'i', 'Ï'=>'I', 'ì'=>'i', 'Ì'=>'I', 'î'=>'i', 'Î'=>'I', 'ľ'=>'l',
		'Ľ'=>'L', 'ĺ'=>'l', 'Ĺ'=>'L', 'ń'=>'n', 'Ń'=>'N', 'ň'=>'n', 'Ň'=>'N',
		'ñ'=>'n', 'Ñ'=>'N', 'ó'=>'o', 'Ó'=>'O', 'ö'=>'o', 'Ö'=>'O', 'ô'=>'o',
		'Ô'=>'O', 'ò'=>'o', 'Ò'=>'O', 'õ'=>'o', 'Õ'=>'O', 'ő'=>'o', 'Ő'=>'O',
		'ř'=>'r', 'Ř'=>'R', 'ŕ'=>'r', 'Ŕ'=>'R', 'š'=>'s', 'Š'=>'S', 'ś'=>'s',
		'Ś'=>'S', 'ť'=>'t', 'Ť'=>'T', 'ú'=>'u', 'Ú'=>'U', 'ů'=>'u', 'Ů'=>'U',
		'ü'=>'u', 'Ü'=>'U', 'ù'=>'u', 'Ù'=>'U', 'ũ'=>'u', 'Ũ'=>'U', 'û'=>'u',
		'Û'=>'U', 'ý'=>'y', 'Ý'=>'Y', 'ž'=>'z', 'Ž'=>'Z', 'ź'=>'z', 'Ź'=>'Z',
	];
	return strtr($str, $table);
}
