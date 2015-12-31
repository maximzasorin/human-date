<?php

	// TODO: Учитывать временную зону
	// TODO: Пересмотреть разделение на _differenceDate и _beautyDate

	/**
	* Class for format date (like VK.com)
	*
	*/
	class HumanDate {
		protected $_secondsPerMinute = 60;
		protected $_secondsPerHour = 3600;
		// protected $_secondsPerDay = 86400;
		// protected $_secondsPerMonth = 2592000;
		protected $_secondsPerYear = 31104000;

		protected $_timezone;
		protected $_lang;

		/**
		* Create new object
		*
		*/
		public function __construct($timezone = null, $lang = 'en') {
			$this->_timezone = $timezone;
			$this->_lang = $lang;

			$this->_loadTranslations();
		}

		/**
		* Format date for humans
		*
		* @param timestamp $date
		* @return string
		*/
		public function format($date) {
			if (!is_numeric($date)) {
				$date = strtotime($date);
			}

			$difference = strtotime('now') - $date;

			$past = $difference < 0 ? false : true;
			$difference = abs($difference);

			if ($difference >= $this->_secondsPerHour * 4 + $this->_secondsPerMinute * 45) {
				$humanDate = $this->_beautyDate($date);
			} else {
				$humanDate = $this->_differenceDate($difference, $past);
			}

			return $humanDate;
		}

		protected function _beautyDate($date) {
			$yesterday = strtotime('-1 day');
			$tomorrow = strtotime('+1 day');

			// Day and month
			if (strftime('%d.%m.%y') == strftime('%d.%m.%y', $date)) {
				$beautyDate = $this->_translation('today');
			} elseif (strftime('%d.%m.%y', $yesterday) == strftime('%d.%m.%y', $date)) {
				$beautyDate = $this->_translation('yesterday');
			} elseif (strftime('%d.%m.%y', $tomorrow) == strftime('%d.%m.%y', $date)) {
				$beautyDate = $this->_translation('tomorrow');
			} else {
				// Day
				$beautyDate = strftime('%d', $date);

				// Month
				$month = strftime('%m', $date);
				$beautyDate .= ' ' . $this->_translation('shortMonths', $month - 1);

				// Year
				if (strtotime('now') - $date > $this->_secondsPerYear) {
					$beautyDate .= ' ' . strftime('%y', $date);
				}
			}

			// Time
			$beautyDate .= ' ' . $this->_translation('delimiter');
			$beautyDate .= ' ' . strftime($this->_translation('time'), $date);

			return $beautyDate;
		}

		protected function _differenceDate($difference, $past) {
			// less than 5 seconds
			if ($difference <= 5)
			{
				if ($past) {
					$differenceDate = $this->_translation('justNow');
				} else {
					$differenceDate = $this->_translation('rightNow');
				}
			}
			// from 5 to 45 sec
			elseif ($difference <= 45)
			{
				$differenceDate = $this->_declension('seconds', $difference);
			}
			// from 45 sec to 1 min 45 sec
			elseif ($difference <= 1 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('oneMinute');
			}
			// from 1 min 45 sec to 2 min 45 sec
			elseif ($difference <= 2 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('twoMinutes');
			}
			// from 2 min 45 sec to 3 min 45 sec
			elseif ($difference <= 3 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('threeMinutes');
			}
			// from 3 min 45 sec to 4 min 45 sec
			elseif ($difference <= 4 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('fourMinutes');
			}
			// from 4 min 45 sec to 45 min 45 sec
			elseif ($difference <= 45 * $this->_secondsPerMinute + 45)
			{
				$minutes = round($difference / $this->_secondsPerMinute);
				$differenceDate = $this->_declension('minutes', $minutes);
			}
			// from 45 min 46 sec to 1 hour 45 min 45 sec
			elseif ($difference <= 1 * $this->_secondsPerHour + 45 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('oneHour');
			}
			elseif ($difference <= 2 * $this->_secondsPerHour + 45 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('twoHours');
			}
			elseif ($difference <= 3 * $this->_secondsPerHour + 45 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('threeHours');
			}
			elseif ($difference <= 4 * $this->_secondsPerHour + 45 * $this->_secondsPerMinute + 45)
			{
				$differenceDate = $this->_translation('fourHours');
			}

			// Add ago or after word
			if (
					$differenceDate != $this->_translation('justNow')
					&&
					$differenceDate != $this->_translation('rightNow')
				)
			{
				if ($past) {
					$differenceDate .= ' ' . $this->_translation('ago');
				} else {
					$differenceDate = $this->_translation('after') . ' ' . $differenceDate;
				}
			}

			return $differenceDate;
		}

		/**
		* Return word from translation file
		*
		*/
		protected function _translation($label, $index = null) {
			if ($index === null) {
				$index = 0;
			}

			if (is_array($this->_translations[$label])) {
				return $this->_translations[$label][$index];
			} else {
				return $this->_translations[$label];
			}
		}

		/**
		* 
		*
		*/
		protected function _declension($label, $number) {
			$declension = $this->_translation('declension');
			$index = $declension($number);

			return $number . ' ' . $this->_translation($label, $index);
		}

		/**
		* Load translations from file
		*
		* @return void
		*/
		protected function _loadTranslations() {
			$this->_translations = require_once(__DIR__ . '/lang/' . $this->_lang . '.php');
		}
	}
