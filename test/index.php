<?php

	ini_set('display_errors', 'On');

	include(__DIR__ . '/../humandate.php');

	$humanDate = new HumanDate('Europe/London', 'ru');

	print $humanDate->format('-1 hours -46 seconds');


