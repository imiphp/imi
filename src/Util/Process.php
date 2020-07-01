<?php
namespace Imi\Util;

use Imi\Util\Traits\TSingleton;

/**
 * 进程工具类
 */
class Process
{
    use TSingleton;

    /**
     * 已监听的事件集合
     *
     * @var array
     */
    private $events = [];

    /**
     * 设置异步信号监听
     *
     * @param integer $signalNo
     * @param callable $callback
     * @return boolean
     */
    public static function signal(int $signalNo, callable $callback): bool
    {
        $instance = self::getInstance();
        if(!isset($instance->events[$signalNo]))
        {
            \Swoole\Process::signal($signalNo, function($signalNo){
                foreach(self::getInstance()->events[$signalNo] ?? [] as $callback)
                {
                    $callback($signalNo);
                }
            });
        }
        $instance->events[$signalNo][] = $callback;
        return true;
    }

}
