<?php

	// TODO: Join _differenceDate and _beauty

	/**
	* Class for format date (like VK.com)
	*
	*/
	class HumanDate {
		protected $timezone;
		protected $lang;

		/**
		* Create new object
		* 
		* @param string $timezone
		* @param string $lang
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
		* @param mixed $date
		* @return string
		*/
		public function format($date) {
			if (!($date instanceof DateTime)) {
				if (is_numeric($date)) {
					$dateObject = new DateTime('now', $this->timezone);
					$date = $dateObject->setTimestamp($date);
				} else {
					$date = new DateTime($date, $this->timezone);
				}
			}

			if ($this->difference($date) > 4 * $this->hour() + 45 * $this->minute() + 45) {
				$humanDate = $this->beauty($date);
			} else {
				$humanDate = $this->timeDifference($date);
			}

			return $humanDate;
		}

		/**
		* 
		*
		* @param DateTime $date
		* @param boolean $shortMonths
		*/
		protected function beauty($date, $shortMonths = true) {
			// Simple date
			if ($this->isToday($date)) {
				$beauty = $this->translation('today');
			} elseif ($this->isYesterday($date)) {
				$beauty = $this->translation('yesterday');
			} elseif ($this->isTomorrow($date)) {
				$beauty = $this->translation('tomorrow');
			} else {
				// Day
				$beauty = $date->format('j');

				// Month
				$month = $date->format('m');

				if ($shortMonths) {
					$beauty .= ' ' . $this->translation('shortMonths', $month - 1);
				} else {
					$beauty .= ' ' . $this->translation('months', $month - 1);
				}

				// Year
				if ($this->difference($date) > $this->year()) {
					$beauty .= ' ' . $date->format('y');
				}
			}

			// Time
			$beauty .= ' ' . $this->translation('delimiter');
			$beauty .= ' ' . $date->format($this->translation('time'));

			return $beauty;
		}

		/**
		* 
		*
		* @param DateTime $date
		* @return string
		*/
		protected function timeDifference($date) {
			$difference = $this->difference($date);

			if ($difference < 5)
			{
				if ($this->isPast($date)) {
					$timeDifference = $this->translation('justNow');
				} else {
					$timeDifference = $this->translation('rightNow');
				}
			} else {
				if ($difference < 45)
				{
					$timeDifference = $this->declension('seconds', $difference);
				}
				elseif ($difference < 1 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('oneMinute');
				}
				elseif ($difference < 2 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('twoMinutes');
				}
				elseif ($difference < 3 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('threeMinutes');
				}
				elseif ($difference < 4 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('fourMinutes');
				}
				elseif ($difference < 45 * $this->minute() + 45)
				{
					$minutes = round($difference / $this->minute());
					$timeDifference = $this->declension('minutes', $minutes);
				}
				elseif ($difference < 1 * $this->hour() + 45 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('oneHour');
				}
				elseif ($difference < 2 * $this->hour() + 45 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('twoHours');
				}
				elseif ($difference < 3 * $this->hour() + 45 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('threeHours');
				}
				elseif ($difference < 4 * $this->hour() + 45 * $this->minute() + 45)
				{
					$timeDifference = $this->translation('fourHours');
				}

				if ($this->isPast($date)) {
					$timeDifference .= ' ' . $this->translation('ago');
				} else {
					$timeDifference = $this->translation('after') . ' ' . $timeDifference;
				}
			}

			return $timeDifference;
		}

		/**
		* Return difference between two date in seconds
		*
		* @param DateTime $date
		* @return integer
		*/
		protected function difference($date) {
			return abs($this->now()->getTimestamp() - $date->getTimestamp());
		}

		/**
		* Return seconds in minute
		*
		* @return integer
		*/
		protected function minute() {
			return 60;
		}

		/**
		* Return seconds in hour
		*
		* @return integer
		*/
		protected function hour() {
			return 3600;
		}

		/**
		* Return seconds in year
		*
		* @return integer
		*/
		protected function year() {
			return 31104000;
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
		* @param object $date
		* @return boolean
		*/
		protected function isToday($date) {
			$now = $this->now();

			return $now->format('d.m.Y') == $date->format('d.m.Y');
		}

		/**
		* Return true if the date is yesterday
		*
		* @param object $date
		* @return boolean
		*/
		protected function isYesterday($date) {
			$yesterday = $this->now()->modify('-1 day');

			return $yesterday->format('d.m.Y') == $date->format('d.m.Y');
		}

		/**
		* Return true if the date is tomorrow
		*
		* @param object $date
		* @return boolean
		*/
		protected function isTomorrow($date) {
			$tomorrow = $this->now()->modify('-1 day');

			return $tomorrow->format('d.m.Y') == $date->format('d.m.Y');
		}

		/**
		* Return true if the date is passed
		*
		* @param object $date
		* @return boolean
		*/
		protected function isPast($date) {
			return ($this->now()->getTimestamp() - $date->getTimestamp() >= 0);
		}

		/**
		* Return word from translation file
		*
		* @param string $label
		* @param integer $index
		* @return string
		*/
		protected function translation($label, $index = 0) {
			if (is_array($this->translations[$label])) {
				return $this->translations[$label][$index];
			} else {
				return $this->translations[$label];
			}
		}

		/**
		* 
		*
		* @param string $label
		* @param integer $number
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
