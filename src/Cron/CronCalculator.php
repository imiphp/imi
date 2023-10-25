<?php

declare(strict_types=1);

namespace Imi\Cron;

use Imi\Bean\Annotation\Bean;

/**
 * 定时规则计算器.
 */
#[Bean(name: 'CronCalculator')]
class CronCalculator
{
    /**
     * 获取下一次执行时间.
     *
     * @param \Imi\Cron\CronRule[] $cronRules
     */
    public function getNextTickTime(int $lastTime, array $cronRules): ?int
    {
        $times = [];
        foreach ($cronRules as $cronRule)
        {
            if ($result = $this->parseN($cronRule, $lastTime))
            {
                $times[] = $result;
                continue;
            }

            $years = $this->getAllYear($cronRule->getYear(), $lastTime);
            $months = $this->getAllMonth($cronRule->getMonth(), $lastTime);
            $weeks = $this->getAllWeek($cronRule->getWeek(), $lastTime);
            $days = $this->getAllDay($cronRule->getDay(), $lastTime);
            $hours = $this->getAllHour($cronRule->getHour(), $lastTime);
            $minutes = $this->getAllMinute($cronRule->getMinute(), $lastTime);
            $seconds = $this->getAllSecond($cronRule->getSecond(), $lastTime);
            $time = $this->generateTime($lastTime, $years, $months, $weeks, $days, $hours, $minutes, $seconds, $cronRule->getDelayMin(), $cronRule->getDelayMax());
            if (null !== $time)
            {
                $times[] = $time;
            }
        }
        if (isset($times[1]))
        {
            return min(...$times);
        }
        else
        {
            return $times[0] ?? null;
        }
    }

    private function generateTime(int $lastTime, array $years, array $months, array $weeks, array $days, array $hours, array $minutes, array $seconds, int $delayMin, int $delayMax): ?int
    {
        if ($lastTime < 0)
        {
            $lastTime = time();
        }
        $nowYear = (int) date('Y', $lastTime);
        $nowMonth = (int) date('m', $lastTime);
        $nowDay = (int) date('d', $lastTime);
        $nowHour = (int) date('H', $lastTime);
        $nowMinute = (int) date('i', $lastTime);
        $nowSecond = (int) date('s', $lastTime);
        foreach ($years as $year)
        {
            if ($year < $nowYear)
            {
                continue;
            }
            foreach ($months ?: [] as $month)
            {
                if ($year == $nowYear && $month < $nowMonth)
                {
                    continue;
                }
                foreach ($days ?: [] as $day)
                {
                    if ('year' === $day)
                    {
                        continue;
                    }
                    if ('year' === $days[0])
                    {
                        if ($day < 0)
                        {
                            $timestamp = strtotime($year . '-12-31') + 86400 * ((int) $day + 1);
                        }
                        else
                        {
                            $timestamp = strtotime($year . '-01-01') + 86400 * ((int) $day - 1);
                        }
                        [$y, $m, $d] = explode('-', date('Y-m-d', $timestamp));
                        $y = (int) $y;
                        $m = (int) $m;
                        $d = (int) $d;
                        if (($y == $nowYear && (($m == $nowMonth && $d < $nowDay) || ($m < $nowMonth))) || !\in_array(date('N', $timestamp), $weeks))
                        {
                            continue;
                        }
                        $result = $this->parseHis($y, $m, $d, $hours, $minutes, $seconds, $nowYear, $nowMonth, $nowDay, $nowHour, $nowMinute, $nowSecond, $delayMin, $delayMax);
                    }
                    else
                    {
                        if ($day < 0)
                        {
                            $day = (int) date('d', strtotime($year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'))) + 86400 * ((int) $day + 1));
                        }
                        if (($year == $nowYear && $month == $nowMonth && $day < $nowDay) || !\in_array(date('N', strtotime("{$year}-{$month}-{$day}")), $weeks))
                        {
                            continue;
                        }
                        $result = $this->parseHis((int) $year, (int) $month, (int) $day, $hours, $minutes, $seconds, $nowYear, $nowMonth, $nowDay, $nowHour, $nowMinute, $nowSecond, $delayMin, $delayMax);
                    }
                    if (null !== $result)
                    {
                        return $result;
                    }
                }
            }
        }

        return null;
    }

    private function parseHis(int $year, int $month, int $day, ?array $hours, ?array $minutes, ?array $seconds, int $nowYear, int $nowMonth, int $nowDay, int $nowHour, int $nowMinute, int $nowSecond, int $delayMin, int $delayMax): ?int
    {
        foreach ($hours ?: [] as $hour)
        {
            if ($year == $nowYear && $month == $nowMonth && $day == $nowDay && $hour < $nowHour)
            {
                continue;
            }
            foreach ($minutes ?: [] as $minute)
            {
                if ($year == $nowYear && $month == $nowMonth && $day == $nowDay && $hour == $nowHour && $minute < $nowMinute)
                {
                    continue;
                }
                foreach ($seconds ?: [] as $second)
                {
                    if ($year == $nowYear && $month == $nowMonth && $day == $nowDay && $hour == $nowHour && $minute == $nowMinute && $second <= $nowSecond)
                    {
                        continue;
                    }

                    $time = strtotime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
                    if (0 !== $delayMin || 0 !== $delayMax)
                    {
                        $time += random_int($delayMin, $delayMax);
                    }

                    return $time;
                }
            }
        }

        return null;
    }

    public function getAll(string $rule, string $name, int $min, int $max, string $dateFormat, int $lastTime): array
    {
        $originRule = $rule;
        if ('day' === $name && str_starts_with($rule, 'year '))
        {
            $rule = substr($rule, 5);
        }
        // 所有
        if ('*' === $rule)
        {
            return range($min, $max);
        }
        // 区间
        if (strpos($rule, '-') > 0 && !str_contains($rule, '-,') && !str_contains($rule, ',-'))
        {
            [$begin, $end] = explode('-', substr($rule, 1), 2);
            $begin = $rule[0] . $begin;
            if ('year' !== $name)
            {
                if ('day' !== $name)
                {
                    // 负数支持
                    if ($begin < $min)
                    {
                        $begin = $max + 1 + (int) $begin;
                    }
                    if ($end < $min)
                    {
                        $end = $max + 1 + (int) $end;
                    }
                }
                if ($begin < $min)
                {
                    throw new \InvalidArgumentException("@Cron({$name}={$originRule}) is invalid, begin value must be >= {$min}");
                }
            }
            if ($begin > $max)
            {
                throw new \InvalidArgumentException("@Cron({$name}={$originRule}) is invalid, begin value must be <= {$max}");
            }
            if ($end > $max)
            {
                throw new \InvalidArgumentException("@Cron({$name}={$originRule}) is invalid, end value must be <= {$max}");
            }

            return range($begin, $end);
        }
        // 步长
        if ('n' === ($rule[-1] ?? ''))
        {
            $step = (int) substr($rule, 0, -1);
            if ($step <= 0)
            {
                throw new \InvalidArgumentException("@Cron({$name}={$originRule}) is invalid, the value of step must be >= 1n and <= {$max}n");
            }
            if ($lastTime >= $min)
            {
                $min = date($dateFormat, $lastTime) % $step;
            }
            if ($step > $max - $min)
            {
                throw new \InvalidArgumentException("@Cron({$name}={$originRule}) is invalid, the value of step must be >= 1n and <= {$max}n");
            }

            return range($min, $max, $step);
        }
        // 列表
        $list = explode(',', $rule);
        // 处理负数
        foreach ($list as &$item)
        {
            if ($item < $min)
            {
                if ('year' === $name)
                {
                    continue;
                }
                elseif ('day' !== $name)
                {
                    $item = $max + 1 + (int) $item;
                }
            }
            elseif ($item > $max)
            {
                throw new \InvalidArgumentException("@Cron({$name}={$originRule}) is invalid, the value must be <= {$max}");
            }
        }
        // 从小到大排序
        sort($list, \SORT_NUMERIC);

        return $list;
    }

    /**
     * 获取所有年份可能性.
     */
    public function getAllYear(string $year, int $lastTime): array
    {
        $min = (int) date('Y', $lastTime);
        $max = 2100; // 我觉得 2100 年不可能还在用这个代码了吧……

        return $this->getAll($year, 'year', $min, $max, 'Y', $lastTime);
    }

    /**
     * 获取所有月份可能性.
     */
    public function getAllMonth(string $month, int $lastTime): array
    {
        return $this->getAll($month, 'month', 1, 12, 'm', $lastTime);
    }

    /**
     * 获取所有日期可能性.
     */
    public function getAllDay(string $day, int $lastTime): array
    {
        if (str_starts_with($day, 'year '))
        {
            $list = $this->getAll($day, 'day', 1, 366, 'd', $lastTime);
            array_unshift($list, 'year');
        }
        else
        {
            $list = $this->getAll($day, 'day', 1, 31, 'd', $lastTime);
        }
        $negatives = [];
        foreach ($list as $i => $value)
        {
            if ($value < 0)
            {
                $negatives[] = $value;
                unset($list[$i]);
            }
            else
            {
                break;
            }
        }
        rsort($negatives, \SORT_NUMERIC);

        return array_values(array_merge($list, $negatives));
    }

    /**
     * 获取所有周的可能性.
     */
    public function getAllWeek(string $week, int $lastTime): array
    {
        return $this->getAll($week, 'week', 1, 7, 'N', $lastTime);
    }

    /**
     * 获取所有小时可能性.
     */
    public function getAllHour(string $hour, int $lastTime): array
    {
        return $this->getAll($hour, 'hour', 0, 23, 'H', $lastTime);
    }

    /**
     * 获取所有分钟可能性.
     */
    public function getAllMinute(string $minute, int $lastTime): array
    {
        return $this->getAll($minute, 'minute', 0, 59, 'i', $lastTime);
    }

    /**
     * 获取所有秒数可能性.
     *
     * @param string $second
     * @param int    $lastTime
     */
    public function getAllSecond($second, $lastTime): array
    {
        return $this->getAll($second, 'second', 0, 59, 's', $lastTime);
    }

    /**
     * 处理 2n、3n……格式.
     *
     * @return int|false
     */
    private function parseN(CronRule $cronRule, int $lastTime)
    {
        if ($lastTime < 0)
        {
            return false;
        }
        if (str_ends_with($rule = $cronRule->getSecond(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            if ($value < 1 || $value > 59)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(second=%s) is invalid, the value of step must be >= 1n and <= 59n', $rule));
            }

            return $lastTime + $value;
        }
        if (str_ends_with($rule = $cronRule->getMinute(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            if ($value < 1 || $value > 59)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(minute=%s) is invalid, the value of step must be >= 1n and <= 59n', $rule));
            }

            return $lastTime + $value * 60;
        }
        if (str_ends_with($rule = $cronRule->getHour(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            if ($value < 1 || $value > 23)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(hour=%s) is invalid, the value of step must be >= 1n and <= 23n', $rule));
            }

            return $lastTime + $value * 3600;
        }
        if (str_ends_with($rule = $cronRule->getDay(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            $min = 1;
            if (str_starts_with($rule, 'year '))
            {
                $max = 366;
            }
            else
            {
                $max = 31;
            }
            if ($value < 1 || $value > 31)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(day=%s) is invalid, the value of step must be >= %dn and <= %dn', $rule, $min, $max));
            }

            return $lastTime + $value * 86400;
        }
        if (str_ends_with($rule = $cronRule->getWeek(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            if ($value < 1 || $value > 7)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(week=%s) is invalid, the value of step must be >= 1n and <= 7n', $rule));
            }

            return $lastTime + $value * 604800;
        }
        if (str_ends_with($rule = $cronRule->getMonth(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            if ($value < 1 || $value > 12)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(month=%s) is invalid, the value of step must be >= 1n and <= 12n', $rule));
            }

            return strtotime('+' . $value . ' month', $lastTime);
        }
        if (str_ends_with($rule = $cronRule->getYear(), 'n'))
        {
            $value = (int) substr($rule, 0, -1);
            if ($value < 1 || $value > 2100)
            {
                throw new \InvalidArgumentException(sprintf('@Cron(year=%s) is invalid, the value of step must be >= 1n and <= 2100n', $rule));
            }

            return strtotime('+' . substr($rule, 0, -1) . ' year', $lastTime);
        }

        return false;
    }
}
