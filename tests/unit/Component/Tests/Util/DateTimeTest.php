<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\DateTime;

/**
 * @testdox Imi\Util\DateTimeTest
 */
class DateTimeTest extends BaseTest
{
    /**
     * @testdox getSecondsByInterval
     */
    public function testGetSecondsByInterval(): void
    {
        $this->go(function (): void {
            $di = new \DateInterval('PT1S');
            $sec = DateTime::getSecondsByInterval($di);
            $this->assertTrue($sec <= 1);
        }, null, 3);
    }

    public function testGetYesterday(): void
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->assertEquals($yesterday, DateTime::getYesterday('Y-m-d'));
        $this->assertGreaterThanOrEqual(strtotime($yesterday), $ts = DateTime::getYesterday());
        $this->assertLessThanOrEqual(strtotime($yesterday) + 86399, $ts);

        $timestamp = strtotime('2018-06-21 12:34:56');
        $this->assertEquals('2018-06-20 12:34:56', DateTime::getYesterday('Y-m-d H:i:s', $timestamp));
    }

    public function testGetTomorrow(): void
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $this->assertEquals($tomorrow, DateTime::getTomorrow('Y-m-d'));
        $this->assertGreaterThanOrEqual(strtotime($tomorrow), $ts = DateTime::getTomorrow());
        $this->assertLessThanOrEqual(strtotime($tomorrow) + 86399, $ts);

        $timestamp = strtotime('2018-06-21 12:34:56');
        $this->assertEquals('2018-06-22 12:34:56', DateTime::getTomorrow('Y-m-d H:i:s', $timestamp));
    }

    public function testGetNextWeek(): void
    {
        $this->assertEquals(DateTime::getNextWeek(), DateTime::getNextWeek());

        $timestamp = strtotime('2018-06-18 12:34:56');
        $nextDateTime = '2018-06-25 12:34:56';
        for ($i = 0; $i < 7; ++$i)
        {
            $this->assertEquals($nextDateTime, DateTime::getNextWeek(1, 'Y-m-d H:i:s', $timestamp), 'Wrong next week: ' . date('Y-m-d H:i:s', $timestamp));
            $this->assertEquals(strtotime($nextDateTime), DateTime::getNextWeek(1, null, $timestamp));
            $timestamp += 86400;
        }
    }

    public function testGetPrevWeek(): void
    {
        $this->assertEquals(DateTime::getPrevWeek(), DateTime::getPrevWeek());

        $timestamp = strtotime('2018-06-18 12:34:56');
        $lastDateTime = '2018-06-11 12:34:56';
        for ($i = 0; $i < 7; ++$i)
        {
            $this->assertEquals($lastDateTime, DateTime::getPrevWeek(1, 'Y-m-d H:i:s', $timestamp), 'Wrong last week: ' . date('Y-m-d H:i:s', $timestamp));
            $this->assertEquals(strtotime($lastDateTime), DateTime::getPrevWeek(1, null, $timestamp));
            $timestamp += 86400;
        }
    }

    public function testGetWeekOfMonth(): void
    {
        $this->assertEquals(DateTime::getWeekOfMonth(), DateTime::getWeekOfMonth());
        $this->assertEquals(4, DateTime::getWeekOfMonth(strtotime('2018-06-21 12:34:56')));
        $this->assertEquals(1, DateTime::getWeekOfMonth(strtotime('2023-01-01')));
        $this->assertEquals(5, DateTime::getWeekOfMonth(strtotime('2023-05-30')));
    }

    public function testGetMonthWeekCount(): void
    {
        $this->assertEquals(5, DateTime::getMonthWeekCount(2018, 6));
        $this->assertEquals(4, DateTime::getMonthWeekCount(2021, 2));
        $this->assertEquals(6, DateTime::getMonthWeekCount(2023, 1));
        $this->assertEquals(5, DateTime::getMonthWeekCount(2023, 2));
        $this->assertEquals(5, DateTime::getMonthWeekCount(2023, 5));
    }
}
