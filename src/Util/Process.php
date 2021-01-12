<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Util\Traits\TSingleton;

/**
 * 进程工具类.
 */
class Process
{
    use TSingleton;

    /**
     * 已监听的事件集合.
     *
     * @var array
     */
    private array $events = [];

    /**
     * 设置异步信号监听.
     *
     * @param int      $signalNo
     * @param callable $callback
     * @param bool     $inheritable 是否可被子进程继承
     *
     * @return bool
     */
    public static function signal(int $signalNo, callable $callback, bool $inheritable = false): bool
    {
        $instance = self::getInstance();
        if (!isset($instance->events[$signalNo]))
        {
            \Swoole\Process::signal($signalNo, function (int $signalNo) {
                foreach (self::getInstance()->events[$signalNo] ?? [] as $callbacks)
                {
                    foreach ($callbacks as $callback)
                    {
                        $callback($signalNo);
                    }
                }
            });
        }
        $instance->events[$signalNo][$inheritable][] = $callback;

        return true;
    }

    /**
     * 清理不可继承的监听.
     *
     * @return void
     */
    public static function clearNotInheritableSignalListener()
    {
        $instance = self::getInstance();
        foreach ($instance->events as &$events)
        {
            if (isset($events[false]))
            {
                foreach ($events[false] as $k => $v)
                {
                    unset($events[false][$k]);
                }
            }
        }
    }
}
