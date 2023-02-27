<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Cron;

use Imi\App;
use Imi\Cron\CronCalculator;
use Imi\Cron\CronRule;
use Imi\Test\BaseTest;

/**
 * @testdox CronCalculator
 */
class CronCalculatorTest extends BaseTest
{
    /**
     * @return mixed
     */
    public function testInit()
    {
        $cronCalculator = App::getBean('CronCalculator');
        $this->assertTrue(true);

        return $cronCalculator;
    }

    /**
     * @depends testInit
     */
    public function testYear(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '*']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2018']),
        ]));
        $this->assertEquals(strtotime('2019-01-01 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2017,2019']),
        ]));
        $this->assertEquals(strtotime('2019-01-01 00:00:01'), $cronCalculator->getNextTickTime(strtotime('2019-01-01 00:00:00'), [
            new CronRule(['year' => '2019-2020']),
        ]));
        $this->assertEquals(strtotime('2020-06-21 12:34:56'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2n']),
        ]));
        $this->assertNull($cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '2008-2017']),
        ]));
        $this->assertNull($cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '-1']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['year' => '2018-2101']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(year=2018-2101) is invalid, end value must be <= 2100', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['year' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(year=0n) is invalid, the value of step must be >= 1n and <= 2100n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['year' => '2101n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(year=2101n) is invalid, the value of step must be >= 1n and <= 2100n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testMonth(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '*']),
        ]));
        $this->assertEquals(strtotime('2018-07-01 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '7']),
        ]));
        $this->assertEquals(strtotime('2018-12-01 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-07-01 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-11-01 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-09-21 12:34:56'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '3n']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['month' => '13']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(month=13) is invalid, the value must be <= 12', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['month' => '13-14']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(month=13-14) is invalid, begin value must be <= 12', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-12-01 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['month' => '-1-12']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['month' => '12-13']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(month=12-13) is invalid, end value must be <= 12', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['month' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(month=0n) is invalid, the value of step must be >= 1n and <= 12n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['month' => '13n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(month=13n) is invalid, the value of step must be >= 1n and <= 12n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testWeek(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '*']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '5']),
        ]));
        $this->assertEquals(strtotime('2018-06-24 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-23 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '3,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '5-7']),
        ]));
        $this->assertEquals(strtotime('2018-06-23 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '-2--1']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['week' => '8']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(week=8) is invalid, the value must be <= 7', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['week' => '8-9']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(week=8-9) is invalid, begin value must be <= 7', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-06-24 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '-1-7']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['week' => '7-8']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(week=7-8) is invalid, end value must be <= 7', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['week' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(week=0n) is invalid, the value of step must be >= 1n and <= 7n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['week' => '8n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(week=8n) is invalid, the value of step must be >= 1n and <= 7n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testDay(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-07-11 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-01-31 00:00:00'), $cronCalculator->getNextTickTime(strtotime('2018-01-01'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-02-28 00:00:00'), $cronCalculator->getNextTickTime(strtotime('2018-02-02'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2016-02-29 00:00:00'), $cronCalculator->getNextTickTime(strtotime('2016-02-02'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-04-30 00:00:00'), $cronCalculator->getNextTickTime(strtotime('2018-04-04'), [
            new CronRule(['day' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-07-05 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-07-07 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-29 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-24 12:34:56'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '3n']),
        ]));

        $this->assertEquals(strtotime('2019-01-11 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year 11']),
        ]));
        $this->assertEquals(strtotime('2018-12-31 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year -1']),
        ]));
        $this->assertEquals(strtotime('2019-01-05 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year 5,6']),
        ]));
        $this->assertEquals(strtotime('2019-01-07 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year 7-9']),
        ]));
        $this->assertEquals(strtotime('2018-12-30 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year -2--1']),
        ]));

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => '32']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=32) is invalid, the value must be <= 31', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => '32-33']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=32-33) is invalid, begin value must be <= 31', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-06-30 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => '-1-31']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => '31-32']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=31-32) is invalid, end value must be <= 31', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=0n) is invalid, the value of step must be >= 1n and <= 31n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => '32n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=32n) is invalid, the value of step must be >= 1n and <= 31n', $e->getMessage(), $e->getMessage());
        }

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => 'year 367']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=year 367) is invalid, the value must be <= 366', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => 'year 367-368']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=year 367-368) is invalid, begin value must be <= 366', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-12-31 00:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['day' => 'year -1-366']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => 'year 366-367']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=year 366-367) is invalid, end value must be <= 366', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => 'year 0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=year 0n) is invalid, the value of step must be >= 1n and <= 366n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => 'year 367n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=year 367n) is invalid, the value of step must be >= 1n and <= 366n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testHour(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-06-22 11:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 23:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 05:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-22 07:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 22:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 15:34:56'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '3n']),
        ]));

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['hour' => '24']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(hour=24) is invalid, the value must be <= 23', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['hour' => '24-25']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(hour=24-25) is invalid, begin value must be <= 23', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-06-21 23:00:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '-1-23']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['hour' => '23-24']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(hour=23-24) is invalid, end value must be <= 23', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['hour' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(hour=0n) is invalid, the value of step must be >= 1n and <= 23n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['hour' => '24n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(hour=24n) is invalid, the value of step must be >= 1n and <= 23n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testMinute(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-06-21 13:11:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:59:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 13:05:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 13:07:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:58:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:37:56'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '3n']),
        ]));

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['minute' => '60']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(minute=60) is invalid, the value must be <= 59', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['minute' => '60-61']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(minute=60-61) is invalid, begin value must be <= 59', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-06-21 12:59:00'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '-1-59']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['minute' => '59-60']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(minute=59-60) is invalid, end value must be <= 59', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['minute' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(minute=0n) is invalid, the value of step must be >= 1n and <= 59n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['minute' => '60n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(minute=60n) is invalid, the value of step must be >= 1n and <= 59n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testSecond(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        $this->assertEquals(strtotime('2018-06-21 12:34:57'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '*']),
        ]));

        $this->assertEquals(strtotime('2018-06-21 12:35:11'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '11']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:59'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '-1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:35:05'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '5,6']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:35:07'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '7-9']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:58'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '-2--1']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 12:34:59'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '3n']),
        ]));

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['second' => '60']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(second=60) is invalid, the value must be <= 59', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['second' => '60-61']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(second=60-61) is invalid, begin value must be <= 59', $e->getMessage(), $e->getMessage());
        }
        $this->assertEquals(strtotime('2018-06-21 12:34:59'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['second' => '-1-59']),
        ]));
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['second' => '59-60']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(second=59-60) is invalid, end value must be <= 59', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['second' => '0n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(second=0n) is invalid, the value of step must be >= 1n and <= 59n', $e->getMessage(), $e->getMessage());
        }
        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['second' => '60n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(second=60n) is invalid, the value of step must be >= 1n and <= 59n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testAll(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        // 每天 0 点执行一次
        $this->assertEquals(strtotime('2018-06-22 00:00:00'), $lastTime = $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '0', 'minute' => '0', 'second' => '0']),
        ]));
        $this->assertEquals(strtotime('2018-06-23 00:00:00'), $lastTime = $cronCalculator->getNextTickTime($lastTime, [
            new CronRule(['hour' => '0', 'minute' => '0', 'second' => '0']),
        ]));

        // 每 15 分钟执行一次
        $this->assertEquals(strtotime('2018-06-21 12:49:56'), $lastTime = $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['minute' => '15n']),
        ]));
        $this->assertEquals(strtotime('2018-06-21 13:04:56'), $lastTime = $cronCalculator->getNextTickTime($lastTime, [
            new CronRule(['minute' => '15n']),
        ]));

        // 每周一中午 12 点执行
        $this->assertEquals(strtotime('2018-06-25 12:00:00'), $lastTime = $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['week' => '1', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));
        $this->assertEquals(strtotime('2018-07-02 12:00:00'), $lastTime = $cronCalculator->getNextTickTime($lastTime, [
            new CronRule(['week' => '1', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));

        // 每月倒数第 3 天中午 12 点
        $this->assertEquals(strtotime('2019-02-26 12:00:00'), $lastTime = $cronCalculator->getNextTickTime(strtotime('2019-01-31 12:00:00'), [
            new CronRule(['day' => '-3', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));
        $this->assertEquals(strtotime('2016-02-27 12:00:00'), $lastTime = $cronCalculator->getNextTickTime(strtotime('2016-01-31 12:00:00'), [
            new CronRule(['day' => '-3', 'hour' => '12', 'minute' => '0', 'second' => '0']),
        ]));
    }

    /**
     * @depends testInit
     *
     * @see https://github.com/imiphp/imi/issues/51
     */
    public function testBug51(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');

        $this->assertEquals(strtotime('2079-06-21 12:34:56'), $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['year' => '61n']),
        ]));

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['year' => '2101n']),
            ]);
            $this->assertTrue(false);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(year=2101n) is invalid, the value of step must be >= 1n and <= 2100n', $e->getMessage(), $e->getMessage());
        }

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['month' => '61n']),
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(month=61n) is invalid, the value of step must be >= 1n and <= 12n', $e->getMessage(), $e->getMessage());
        }

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['day' => '61n']),
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(day=61n) is invalid, the value of step must be >= 1n and <= 31n', $e->getMessage(), $e->getMessage());
        }

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['hour' => '61n']),
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(hour=61n) is invalid, the value of step must be >= 1n and <= 23n', $e->getMessage(), $e->getMessage());
        }

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['minute' => '61n']),
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(minute=61n) is invalid, the value of step must be >= 1n and <= 59n', $e->getMessage(), $e->getMessage());
        }

        try
        {
            $cronCalculator->getNextTickTime($beginTime, [
                new CronRule(['second' => '61n']),
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->assertEquals('@Cron(second=61n) is invalid, the value of step must be >= 1n and <= 59n', $e->getMessage(), $e->getMessage());
        }
    }

    /**
     * @depends testInit
     */
    public function testDelay(CronCalculator $cronCalculator): void
    {
        $beginTime = strtotime('2018-06-21 12:34:56');
        // 每天 0 点执行一次
        $lastTime = $cronCalculator->getNextTickTime($beginTime, [
            new CronRule(['hour' => '0', 'minute' => '0', 'second' => '0', 'delayMin' => 1, 'delayMax' => 3]),
        ]);
        $this->assertTrue($lastTime > strtotime('2018-06-22 00:00:00'));
        $this->assertTrue($lastTime <= strtotime('2018-06-22 00:00:03'));
    }
}
