<?php

	ini_set('display_errors', 'On');

	include(__DIR__ . '/../humandate.php');

	$humanDate = new HumanDate('Europe/Moscow', 'ru');

	print $humanDate->format(strtotime('+3 minutes +45 seconds'));


