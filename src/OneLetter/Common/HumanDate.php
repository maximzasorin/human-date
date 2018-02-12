<?php

namespace OneLetter\Common;

/**
 * Class for format date.
 */
class HumanDate
{
    /**
     * Base timezone for passed dates as string.
     *
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * Language.
     *
     * @var string
     */
    protected $lang;

    /**
     * Translations.
     *
     * @var array
     */
    protected $translations;

    /**
     * Current time.
     *
     * @var \DateTime
     */
    protected $now;

    /**
     * Use short months.
     *
     * @var boolean
     */
    protected $shortMonths = true;

    /**
     * Constructor.
     *
     * @param  mixed  $timezone
     * @param  string  $lang
     * @return void
     */
    public function __construct($timezone = 'UTC', $lang = 'en')
    {
        if ($timezone instanceof \DateTimeZone) {
            $this->timezone = $timezone;
        } else {
            $this->timezone = new \DateTimeZone($timezone);
        }

        $this->lang = $lang;

        if (!file_exists($this->getLangFile())) {
            $this->lang = 'en';
        }

        $this->loadTranslations();
    }

    /**
     * Format date for humans.
     *
     * @param  mixed  $date
     * @param  DateTime  $now
     * @return string
     */
    public function format($date, $now = 'now')
    {
        $date = $this->createDate($date);
        $this->now = $this->createDate($now);

        if ($this->getDistance($date) > 4 * $this->hour() + 45 * $this->minute() + 45) {
            $humanDate = $this->beauty($date);
        } else {
            $humanDate = $this->words($date);
        }

        return $humanDate;
    }

    /**
     * shortMonths property.
     *
     * @param  boolean $shortMonths
     * @return self
     */
    public function shortMonths($shortMonths)
    {
        $this->shortMonths = $shortMonths;

        return $this;
    }

    /**
     * Set translations.
     *
     * @param  array  $translations
     * @return self
     */
    public function translations($translations)
    {
        $this->translations = $translations;

        return $this;
    }

    /**
     * Return date in beauty format.
     *
     * @param  DateTime  $date
     * @return string
     */
    protected function beauty($date)
    {
        $tomorrow = $this->isTomorrow($date);

        // Simple date
        if ($this->isToday($date)) {
            $beauty = $this->translate('today');
        } elseif ($this->isYesterday($date)) {
            $beauty = $this->translate('yesterday');
        } elseif ($tomorrow) {
            $beauty = $this->translate('tomorrow');
        } else {
            // Day
            $beauty = $date->format('j');

            // Month
            $month = $date->format('m');

            if ($this->shortMonths) {
                $beauty .= ' ' . $this->translate('shortMonths', $month - 1);
            } else {
                $beauty .= ' ' . $this->translate('months', $month - 1);
            }

            // Year
            if ($this->getDistance($date) > $this->year()) {
                $beauty .= ' ' . $date->format('y');
            }
        }

        // Time
        $beauty .= ' ' . $this->translate('delimiter');
        $beauty .= ' ' . $date->format($this->translate('time'));

        return $beauty;
    }

    /**
     * Return date in words.
     *
     * Work for date from 0 seconds to 4 hours 45 minutes 45 seconds.
     *
     * @param  DateTime  $date
     * @return string
     */
    protected function words($date)
    {
        $distance = $this->getDistance($date);

        if ($distance < 5) {
            if ($this->isPast($date)) {
                $words = $this->translate('justNow');
            } else {
                $words = $this->translate('rightNow');
            }
        } else {
            if ($distance < 45) {
                $words = $this->decline('seconds', $distance);
            } elseif ($distance < 1 * $this->minute() + 45) {
                $words = $this->translate('oneMinute');
            } elseif ($distance < 2 * $this->minute() + 45) {
                $words = $this->translate('twoMinutes');
            } elseif ($distance < 3 * $this->minute() + 45) {
                $words = $this->translate('threeMinutes');
            } elseif ($distance < 4 * $this->minute() + 45) {
                $words = $this->translate('fourMinutes');
            } elseif ($distance < 45 * $this->minute() + 45) {
                $minutes = round($distance / $this->minute());
                $words = $this->decline('minutes', $minutes);
            } elseif ($distance < 1 * $this->hour() + 45 * $this->minute() + 45) {
                $words = $this->translate('oneHour');
            } elseif ($distance < 2 * $this->hour() + 45 * $this->minute() + 45) {
                $words = $this->translate('twoHours');
            } elseif ($distance < 3 * $this->hour() + 45 * $this->minute() + 45) {
                $words = $this->translate('threeHours');
            } elseif ($distance <= 4 * $this->hour() + 45 * $this->minute() + 45) {
                $words = $this->translate('fourHours');
            }

            if ($this->isPast($date)) {
                $words .= ' ' . $this->translate('ago');
            } else {
                $words = $this->translate('after') . ' ' . $words;
            }
        }

        return $words;
    }

    /**
     * Return difference between two date in seconds.
     *
     * @param  DateTime  $date
     * @return integer
     */
    protected function getDistance($date)
    {
        return abs($this->now()->getTimestamp() - $date->getTimestamp());
    }

    /**
     * Return seconds in minute.
     *
     * @return integer
     */
    protected function minute()
    {
        return 60;
    }

    /**
     * Return seconds in hour.
     *
     * @return integer
     */
    protected function hour()
    {
        return 3600;
    }

    /**
     * Return seconds in year.
     *
     * @return integer
     */
    protected function year()
    {
        return 31104000;
    }

    /**
     * Return now DateTime.
     *
     * @return DateTime
     */
    protected function now()
    {
        return clone $this->now;
    }

    /**
     * Create DateTime object from another types.
     *
     * @param  mixed  $date
     * @return DateTime
     */
    protected function createDate($date)
    {
        if ($date instanceof \DateTime) {
            $date = clone $date;
        } else {
            if (is_numeric($date)) {
                $now = new \DateTime('now', $this->timezone);
                $date = $now->setTimestamp($date);
            } else {
                $date = new \DateTime($date, $this->timezone);
            }
        }

        return $date;
    }

    /**
     * Return true if the date is today.
     *
     * @param  DateTime  $date
     * @return boolean
     */
    protected function isToday($date)
    {
        $now = $this->now();

        return $now->format('d.m.Y') == $date->format('d.m.Y');
    }

    /**
     * Return true if the date is yesterday.
     *
     * @param  DateTime  $date
     * @return boolean
     */
    protected function isYesterday($date)
    {
        $yesterday = $this->now()->modify('-1 day');

        return $yesterday->format('d.m.Y') == $date->format('d.m.Y');
    }

    /**
     * Return true if the date is tomorrow.
     *
     * @param  DateTime  $date
     * @return boolean
     */
    protected function isTomorrow($date)
    {
        $tomorrow = $this->now()->modify('+1 day');

        return $tomorrow->format('d.m.Y') == $date->format('d.m.Y');
    }

    /**
     * Return true if the date is passed.
     *
     * @param  DateTime  $date
     * @return boolean
     */
    protected function isPast($date)
    {
        return ($this->now()->getTimestamp() - $date->getTimestamp() >= 0);
    }

    /**
     * Return word from translation file.
     *
     * @param  string  $label
     * @param  integer  $index
     * @return string
     */
    protected function translate($label, $index = 0)
    {
        if (!array_key_exists($label, $this->translations)) {
            return '';
        }

        if (is_array($this->translations[$label])) {
            return $this->translations[$label][$index];
        } else {
            return $this->translations[$label];
        }
    }

    /**
     * Make decline for the world.
     *
     * @param  string  $label
     * @param  integer  $number
     * @return string
     */
    protected function decline($label, $number)
    {
        $declineFunc = $this->translate('decline');

        if ($declineFunc) {
            $index = $declineFunc($number);
        } else {
            $index = 0;
        }

        return $number . ' ' . $this->translate($label, $index);
    }

    /**
     * Load translations from file.
     *
     * @return void
     */
    protected function loadTranslations()
    {
        $this->translations = require($this->getLangFile());
    }

    /**
     * Return path for lang file.
     *
     * @return string
     */
    protected function getLangFile()
    {
        return __DIR__ . '/lang/' . $this->lang . '.php';
    }
}
