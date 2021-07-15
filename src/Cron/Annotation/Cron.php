<?php

declare(strict_types=1);

namespace Imi\Cron\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 定时任务注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string|null $id               任务唯一ID
 * @property string|null $type             任务类型；\Imi\Cron\Consts\CronTaskType 类常量
 * @property mixed       $data             数据
 * @property bool        $force            每次启动服务强制执行
 * @property string      $year             年；指定任务执行年份，默认为 `*`；`*` - 不限制；`2019` - 指定年；`2019-2022` - 指定年份区间；`2019,2021,2022` - 指定多个年份；`2n` - 每 2 年，其它以此类推
 * @property string      $month            月；指定任务执行月份，默认为 `*`；`*` - 不限制；`1` (1 月), `-1` (12 月) - 指定月份，支持负数为倒数的月；`1-6` (1-6 月), `-3--1` (10-12 月) - 指定月份区间，支持负数为倒数的月；`1,3,5,-1` (1、3、5、12 月) - 指定多个月份，支持负数为倒数的月；`2n` - 每 2 个月，其它以此类推
 * @property string      $day              日；指定任务执行日期，默认为 `*`；`*` - 不限制；`1` (1 日), `-1` (每月最后一天) - 指定日期，支持负数为倒数的日期；`1-6` (1-6 日), `-3--1` (每月倒数 3 天) - 指定日期区间，支持负数为倒数的日期；`1,3,5,-1` (每月 1、3、5、最后一天) - 指定多个日期，支持负数为倒数的日期；`2n` - 每 2 天，其它以此类推；`year 1` (一年中的第 1 日), `year -1` (每年最后一天) - 指定一年中的日期，支持负数为倒数的日期；`year 1-6` (一年中的第 1-6 日), `year -3--1` (每年倒数 3 天) - 指定一年中的日期区间，支持负数为倒数的日期；`year 1,3,5,-1` (每年 1、3、5、最后一天) - 指定一年中的多个日期，支持负数为倒数的日期
 * @property string      $week             周几；指定周几执行任务，默认为 `*`；`*` - 不限制；`1` (周一), `-1` (周日) - 指定周几（1-7），支持负数为倒数的周；`1-6` (周一到周六), `-3--1` (周五到周日) - 指定周几，支持负数为倒数的周；`1,3,5,-1` (周一、三、五、日) - 指定多个日期，支持负数为倒数的周
 * @property string      $hour             小时；指定任务执行小时，默认为 `*`；`*` - 不限制；`0` (0 点), `-1` (23 点) - 指定小时，支持负数为倒数的小时；`1-6` (1-6 店), `-3--1` (21-23 点) - 指定小时区间，支持负数为倒数的小时；`1,3,5,-1` (1、3、5、23 点) - 指定多个小时，支持负数为倒数的小时；`2n` - 每 2 小时，其它以此类推
 * @property string      $minute           分钟；指定任务执行分钟，默认为 `*`；`*` - 不限制；`0` (0 分), `-1` (23 分) - 指定分钟，支持负数为倒数的分钟；`1-6` (1-6 分), `-3--1` (57-59 分) - 指定分钟区间，支持负数为倒数的分钟；`1,3,5,-1` (1、3、5、59 分) - 指定多个分钟，支持负数为倒数的分钟；`2n` - 每 2 分钟，其它以此类推
 * @property string      $second           秒；指定任务执行秒，默认为 `*`；`*` - 不限制；`0` (0 秒), `-1` (23 秒) - 指定秒，支持负数为倒数的秒；`1-6` (1-6 秒), `-3--1` (57-59 秒) - 指定秒区间，支持负数为倒数的秒；`1,3,5,-1` (1、3、5、59 秒) - 指定多个秒，支持负数为倒数的秒；`2n` - 每 2 秒，其它以此类推
 * @property string|null $unique           定时任务唯一性设置；当前实例唯一: current；所有实例唯一: all；不唯一: null
 * @property string|null $redisPool        用于锁的 `Redis` 连接池名
 * @property float       $lockWaitTimeout  获取锁超时时间，单位：秒
 * @property float       $maxExecutionTime 最大运行执行时间，单位：秒；该值与分布式锁超时时间共享，默认为 60 秒
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Cron extends Base
{
    /**
     * @param mixed $data
     */
    public function __construct(?array $__data = null, ?string $id = null, ?string $type = null, $data = null, bool $force = false, string $year = '*', string $month = '*', string $day = '*', string $week = '*', string $hour = '*', string $minute = '*', string $second = '*', ?string $unique = null, ?string $redisPool = null, float $lockWaitTimeout = 3, float $maxExecutionTime = 60)
    {
        parent::__construct(...\func_get_args());
    }
}
