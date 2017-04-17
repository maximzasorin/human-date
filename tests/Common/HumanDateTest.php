<?php

namespace OneLetter\Tests\Common;

use OneLetter\Common\HumanDate;

/**
 * Class for test OneLetter\Common\HumanDate.
 */
class HumanDateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Timezone.
     *
     * @var \DateTimeZone
     */
    private $timezone;

    /**
     * Language.
     *
     * @var string
     */
    private $lang;

    /**
     * Object of HumanDate.
     *
     * @var Oneletter\Common\HumanDate
     */
    private $humanDate;

    /**
     * Set up.
     *
     * @return void
     */
    public function setUp()
    {
        $this->timezone = new \DateTimeZone('UTC');
        $this->lang = 'en';

        $this->humanDate = new HumanDate($this->timezone, $this->lang);
    }

    /**
     * Test for \DateTime, timestamps and strings
     *
     * @param  string  $date
     * @param  string  $now
     * @param  string  $expectedDate
     */
    public function assertDateToFormat($date, $now, $expectedDate)
    {
        $dateObject = new \DateTime($date, $this->timezone);
        $nowObject = new \DateTime($now, $this->timezone);

        $dateTimestamp = $dateObject->getTimestamp();
        $nowTimestamp = $nowObject->getTimestamp();

        $formattedDateFromObjects = $this->humanDate->format($dateObject, $nowObject);
        $formattedDateFromTimestamps = $this->humanDate->format($dateTimestamp, $nowTimestamp);
        $formattedDateFromStrings = $this->humanDate->format($date, $now);

        $this->assertEquals($expectedDate, $formattedDateFromObjects);
        $this->assertEquals($expectedDate, $formattedDateFromTimestamps);
        $this->assertEquals($expectedDate, $formattedDateFromStrings);
    }

    /**
     * Test without set now.
     *
     * @return void
     */
    public function testWithoutSetNow()
    {
        $humanDate = new HumanDate($this->timezone, $this->lang);

        $formattedDate = $humanDate->format(new \DateTime('-6 seconds'));
        $expectedDate = '6 seconds ago';

        $this->assertEquals($expectedDate, $formattedDate);
    }

    /**
     * Test without timezone and language.
     *
     * @return void
     */
    public function testWithoutTimezoneAndLanguage()
    {
        $humanDate = new HumanDate;

        $formattedDate = $humanDate->format(new \DateTime('-6 seconds'));
        $expectedDate = '6 seconds ago';

        $this->assertEquals($expectedDate, $formattedDate);
    }

    /**
     * Test short months.
     *
     * @dataProvider dataShortMonths
     *
     * @param  string  $now
     * @param  string  $date
     * @param  string  $expected
     */
    public function testShortMonths($date, $now, $expected)
    {
        $this->humanDate
            ->shortMonths(true);

        $this->assertDateToFormat($date, $now, $expected);
    }

    /**
     * Data for test short months.
     *
     * @return array
     */
    public function dataShortMonths()
    {
        return array(
            array('2015-01-15 00:00:00', '2015-01-01 00:00:00', '15 Jan at 12:00 am'),
            array('2015-02-15 00:00:00', '2015-01-01 00:00:00', '15 Feb at 12:00 am'),
            array('2015-03-15 00:00:00', '2015-01-01 00:00:00', '15 Mar at 12:00 am'),
            array('2015-04-15 00:00:00', '2015-01-01 00:00:00', '15 Apr at 12:00 am'),
            array('2015-05-15 00:00:00', '2015-01-01 00:00:00', '15 May at 12:00 am'),
            array('2015-06-15 00:00:00', '2015-01-01 00:00:00', '15 Jun at 12:00 am'),
            array('2015-07-15 00:00:00', '2015-01-01 00:00:00', '15 Jul at 12:00 am'),
            array('2015-08-15 00:00:00', '2015-01-01 00:00:00', '15 Aug at 12:00 am'),
            array('2015-09-15 00:00:00', '2015-01-01 00:00:00', '15 Sep at 12:00 am'),
            array('2015-10-15 00:00:00', '2015-01-01 00:00:00', '15 Oct at 12:00 am'),
            array('2015-11-15 00:00:00', '2015-01-01 00:00:00', '15 Nov at 12:00 am'),
            array('2015-12-15 00:00:00', '2015-01-01 00:00:00', '15 Dec at 12:00 am'),
        );
    }

    /**
     * Test months.
     *
     * @dataProvider dataMonths
     *
     * @param  string  $now
     * @param  string  $date
     * @param  string  $expected
     */
    public function testMonths($date, $now, $expected)
    {
        $this->humanDate
            ->shortMonths(false);

        $this->assertDateToFormat($date, $now, $expected);
    }

    /**
     * Data for test months.
     *
     * @return array
     */
    public function dataMonths()
    {
        return array(
            array('2015-01-15 00:00:00', '2015-01-01 00:00:00', '15 January at 12:00 am'),
            array('2015-02-15 00:00:00', '2015-01-01 00:00:00', '15 February at 12:00 am'),
            array('2015-03-15 00:00:00', '2015-01-01 00:00:00', '15 March at 12:00 am'),
            array('2015-04-15 00:00:00', '2015-01-01 00:00:00', '15 April at 12:00 am'),
            array('2015-05-15 00:00:00', '2015-01-01 00:00:00', '15 May at 12:00 am'),
            array('2015-06-15 00:00:00', '2015-01-01 00:00:00', '15 June at 12:00 am'),
            array('2015-07-15 00:00:00', '2015-01-01 00:00:00', '15 July at 12:00 am'),
            array('2015-08-15 00:00:00', '2015-01-01 00:00:00', '15 August at 12:00 am'),
            array('2015-09-15 00:00:00', '2015-01-01 00:00:00', '15 September at 12:00 am'),
            array('2015-10-15 00:00:00', '2015-01-01 00:00:00', '15 October at 12:00 am'),
            array('2015-11-15 00:00:00', '2015-01-01 00:00:00', '15 November at 12:00 am'),
            array('2015-12-15 00:00:00', '2015-01-01 00:00:00', '15 December at 12:00 am'),
        );
    }

    /**
     * Test dates in past.
     *
     * @dataProvider dataPastTime
     *
     * @param  string  $now
     * @param  string  $date
     * @param  string  $expected
     */
    public function testPastTime($date, $now, $expected)
    {
        $this->assertDateToFormat($date, $now, $expected);
    }

    /**
     * Data for test dates in past time.
     *
     * @return array
     */
    public function dataPastTime()
    {
        return array(
            array('2015-01-15 00:00:00', '2015-01-15 00:00:01', 'just now'),
            array('2015-01-15 00:00:00', '2015-01-15 00:00:06', '6 seconds ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:00:45', 'one minute ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:01:45', 'two minutes ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:02:45', 'three minutes ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:03:45', 'four minutes ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:04:45', '5 minutes ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:05:29', '5 minutes ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:05:30', '6 minutes ago'),
            array('2015-01-15 00:00:00', '2015-01-15 00:45:45', 'one hour ago'),
            array('2015-01-15 00:00:00', '2015-01-15 01:45:45', 'two hours ago'),
            array('2015-01-15 00:00:00', '2015-01-15 02:45:45', 'three hours ago'),
            array('2015-01-15 00:00:00', '2015-01-15 03:45:45', 'four hours ago'),
            array('2015-01-15 00:00:00', '2015-01-15 04:45:46', 'today at 12:00 am'),
            array('2015-01-15 00:00:00', '2015-01-16 04:45:46', 'yesterday at 12:00 am'),
            array('2015-01-15 00:00:00', '2015-01-17 04:45:46', '15 Jan at 12:00 am'),
        );
    }

    /**
     * Rest date in future
     *
     * @dataProvider dataFutureTime
     *
     * @param  string  $date
     * @param  string  $now
     * @param  string  $expected
     */
    public function testFutureTime($date, $now, $expected)
    {
        $this->assertDateToFormat($date, $now, $expected);
    }

    /**
     * Data for test dates in future time.
     *
     * @return array
     */
    public function dataFutureTime()
    {
        return array(
            array('2015-01-15 00:00:01', '2015-01-15 00:00:00', 'right now'),
            array('2015-01-15 00:00:06', '2015-01-15 00:00:00', 'in a 6 seconds'),
            array('2015-01-15 00:00:45', '2015-01-15 00:00:00', 'in a one minute'),
            array('2015-01-15 00:01:45', '2015-01-15 00:00:00', 'in a two minutes'),
            array('2015-01-15 00:02:45', '2015-01-15 00:00:00', 'in a three minutes'),
            array('2015-01-15 00:03:45', '2015-01-15 00:00:00', 'in a four minutes'),
            array('2015-01-15 00:04:45', '2015-01-15 00:00:00', 'in a 5 minutes'),
            array('2015-01-15 00:05:29', '2015-01-15 00:00:00', 'in a 5 minutes'),
            array('2015-01-15 00:05:30', '2015-01-15 00:00:00', 'in a 6 minutes'),
            array('2015-01-15 00:45:45', '2015-01-15 00:00:00', 'in a one hour'),
            array('2015-01-15 01:45:45', '2015-01-15 00:00:00', 'in a two hours'),
            array('2015-01-15 02:45:45', '2015-01-15 00:00:00', 'in a three hours'),
            array('2015-01-15 03:45:45', '2015-01-15 00:00:00', 'in a four hours'),
            array('2015-01-15 04:45:46', '2015-01-15 00:00:00', 'today at 4:45 am'),
            array('2015-01-16 04:45:46', '2015-01-15 00:00:00', 'tomorrow at 4:45 am'),
            array('2015-01-17 04:45:46', '2015-01-15 00:00:00', '17 Jan at 4:45 am'),
        );
    }
}
