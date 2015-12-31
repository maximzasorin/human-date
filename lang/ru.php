<?php

	/**
	* Translations for Russian
	*
	*/
	return [
		'shortMonths' => ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'],
		'time' => '%H:%M',
		
		'justNow' => 'только что', // Past
		'rightNow' => 'прямо сейчас', // Future
		'seconds' => ['секунду', 'секунды', 'секунд'],
		'oneMinute' => 'минуту',
		'twoMinutes' => 'две минуты',
		'threeMinutes' => 'три минуты',
		'fourMinutes' => 'четыре минуты',
		'minutes' => ['минуту', 'минуты', 'минут'],
		'oneHour' => 'час',
		'twoHours' => 'два часа',
		'threeHours' => 'три часа',
		'fourHours' => 'четыре часа',

		'today' => 'сегодня',
		'yesterday' => 'вчера',
		'tomorrow' => 'завтра',

		'ago' => 'назад',
		'after' => 'через',
		'delimiter' => 'в',

		'declension' => function($number) {
			if ($number >= 10 && $number <= 20) {
				$declension = 2;
			} else {
				$mod = $number % 10;

				if ($mod == 1) {
					$declension = 0;
				} elseif ($mod <= 4) {
					$declension = 1;
				} else {
					$declension = 2;
				}
			}

			return $declension;
		}
	];