<?php

	ini_set('display_errors', 'On');

	include(__DIR__ . '/../humandate.php');

	$humanDate = new HumanDate(null, 'ru');

	print $humanDate->format('-23 days 1 hours -46 seconds');


