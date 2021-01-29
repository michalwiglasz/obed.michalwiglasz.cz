<?php

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
	$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

if ($_SERVER['REMOTE_ADDR'] == '89.177.97.105') {
	header("Location: /zborovska");
	exit(0);
}

header("Location: /fit");
