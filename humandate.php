<?php

	// TODO: Join _differenceDate and _beautyDate
	// TODO: properties $secondsPerMinute, ...

	/**
	* Class for format date (like VK.com)
	*
	*/
	class HumanDate {
		protected $secondsPerMinute = 60;
		protected $secondsPerHour = 3600;
		// protected $secondsPerDay = 86400;
		// protected $secondsPerMonth = 2592000;
		protected $secondsPerYear = 31104000;

		protected $timezone;
		protected $lang;

		/**
		* Create new object
		*
		*/
		public function __construct($timezone = null, $lang = 'en') {
			if ($timezone) {
				$this->timezone = new DateTimeZone($timezone);
			}

			$this->lang = $lang;

			if (!file_exists($this->langFile())) {
				$this->lang = 'en';
			}

			$this->loadTranslations();
		}

		/**
		* Format date for humans
		*
		* @param timestamp $date
		* @return string
		*/
		public function format($date) {
			if (!($date instanceof DateTime)) {
				$date = new DateTime($date, $this->timezone);
			}

			$now = new DateTime('now', $this->timezone);

			$difference = $now->getTimestamp() - $date->getTimestamp();

			if (abs($difference) >= $this->secondsPerHour * 4 + $this->secondsPerMinute * 45 + 45) {
				$humanDate = $this->beautyDate($date);
			} else {
				$humanDate = $this->differenceDate($difference);
			}

			return $humanDate;
		}

		public function beautyDate($date, $shortMonths = true) {
			// Simple date
			if ($this->isToday($date)) {
				$beautyDate = $this->translation('today');
			} elseif ($this->isYesterday($date)) {
				$beautyDate = $this->translation('yesterday');
			} elseif ($this->isTomorrow($date)) {
				$beautyDate = $this->translation('tomorrow');
			} else {
				// Day
				$beautyDate = $date->format('j');

				// Month
				$month = $date->format('m');

				if ($shortMonths) {
					$beautyDate .= ' ' . $this->translation('shortMonths', $month - 1);
				} else {
					$beautyDate .= ' ' . $this->translation('months', $month - 1);
				}

				// Year
				if ($this->now()->getTimestamp() - $date->getTimestamp() > $this->secondsPerYear) {
					$beautyDate .= ' ' . $date->format('Y');
				}
			}

			// Time
			$beautyDate .= ' ' . $this->translation('delimiter');
			$beautyDate .= ' ' . $date->format($this->translation('time'));

			return $beautyDate;
		}

		protected function differenceDate($difference) {
			$differenceAbs = abs($difference);

			// less than 5 seconds
			if ($differenceAbs <= 5)
			{
				if ($difference > 0) {
					$differenceDate = $this->translation('justNow');
				} else {
					$differenceDate = $this->translation('rightNow');
				}
			}
			// from 5 to 45 sec
			elseif ($differenceAbs <= 45)
			{
				$differenceDate = $this->declension('seconds', $difference);
			}
			// from 45 sec to 1 min 45 sec
			elseif ($differenceAbs <= 1 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('oneMinute');
			}
			// from 1 min 45 sec to 2 min 45 sec
			elseif ($differenceAbs <= 2 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('twoMinutes');
			}
			// from 2 min 45 sec to 3 min 45 sec
			elseif ($differenceAbs <= 3 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('threeMinutes');
			}
			// from 3 min 45 sec to 4 min 45 sec
			elseif ($differenceAbs <= 4 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('fourMinutes');
			}
			// from 4 min 45 sec to 45 min 45 sec
			elseif ($differenceAbs <= 45 * $this->secondsPerMinute + 45)
			{
				$minutes = round($differenceAbs / $this->secondsPerMinute);
				$differenceDate = $this->declension('minutes', $minutes);
			}
			// from 45 min 46 sec to 1 hour 45 min 45 sec
			elseif ($differenceAbs <= 1 * $this->secondsPerHour + 45 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('oneHour');
			}
			elseif ($differenceAbs <= 2 * $this->secondsPerHour + 45 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('twoHours');
			}
			elseif ($differenceAbs <= 3 * $this->secondsPerHour + 45 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('threeHours');
			}
			elseif ($differenceAbs <= 4 * $this->secondsPerHour + 45 * $this->secondsPerMinute + 45)
			{
				$differenceDate = $this->translation('fourHours');
			}

			// Add ago or after word
			if (
					$differenceDate != $this->translation('justNow')
					&&
					$differenceDate != $this->translation('rightNow')
				)
			{
				if ($difference > 0) {
					$differenceDate .= ' ' . $this->translation('ago');
				} else {
					$differenceDate = $this->translation('after') . ' ' . $differenceDate;
				}
			}

			return $differenceDate;
		}

		/**
		* Return now DateTime
		*
		* @return object
		*/
		protected function now() {
			return new DateTime('now', $this->timezone);
		}

		/**
		* Return true if the date is today
		*
		* @return boolean
		*/
		protected function isToday($date) {
			$now = $this->now();

			return $now->format('d.m.Y') == $date->format('d.m.Y');
		}

		/**
		* Return true if the date is yesterday
		*
		* @return boolean
		*/
		protected function isYesterday($date) {
			$yesterday = $this->now()->modify('-1 day');

			return $yesterday->format('d.m.Y') == $date->format('d.m.Y');
		}

		/**
		* Return true if the date is tomorrow
		*
		* @return boolean
		*/
		protected function isTomorrow($date) {
			$tomorrow = $this->now()->modify('-1 day');

			return $tomorrow->format('d.m.Y') == $date->format('d.m.Y');
		}

		/**
		* Return word from translation file
		*
		* @return string
		*/
		protected function translation($label, $index = null) {
			if ($index === null) {
				$index = 0;
			}

			if (is_array($this->translations[$label])) {
				return $this->translations[$label][$index];
			} else {
				return $this->translations[$label];
			}
		}

		/**
		* 
		*
		* 
		* @return string
		*/
		protected function declension($label, $number) {
			$declension = $this->translation('declension');
			$index = $declension($number);

			return $number . ' ' . $this->translation($label, $index);
		}

		/**
		* Return path for lang file
		*
		* @return string
		*/
		protected function langFile() {
			return __DIR__ . '/lang/' . $this->lang . '.php';
		}

		/**
		* Load translations from file
		*
		* @return void
		*/
		protected function loadTranslations() {
			$this->translations = require_once($this->langFile());
		}
	}
