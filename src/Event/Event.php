<?php

namespace Imi\Event;

use Imi\Util\Traits\TSingleton;

class Event
{
    use TEvent
    {
        on as public __on;
        one as public __one;
        off as public __off;
        trigger as public __trigger;
    }

    use TSingleton;

    /**
     * 事件监听.
     *
     * @param string $name     事件名称
     * @param mixed  $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int    $priority 优先级，越大越先执行
     *
     * @return void
     */
    public static function on($name, $callback, $priority = 0)
    {
        static::getInstance()->__on($name, $callback, $priority);
    }

    /**
     * 监听事件，仅触发一次
     *
     * @param string $name     事件名称
     * @param mixed  $callback 回调，支持回调函数、基于IEventListener的类名
     * @param int    $priority 优先级，越大越先执行
     *
     * @return void
     */
    public static function one($name, $callback, $priority = 0)
    {
        static::getInstance()->__one($name, $callback, $priority);
    }

    /**
     * 取消事件监听.
     *
     * @param string $name     事件名称
     * @param mixed  $callback 回调，支持回调函数、基于IEventListener的类名
     *
     * @return void
     */
    public static function off($name, $callback)
    {
        static::getInstance()->__off($name, $callback);
    }

    /**
     * 触发事件.
     *
     * @param string $name       事件名称
     * @param array  $data       数据
     * @param mixed  $target     目标对象
     * @param string $paramClass 参数类
     *
     * @return void
     */
    public static function trigger($name, $data = [], $target = null, $paramClass = EventParam::class)
    {
        static::getInstance()->__trigger($name, $data, $target, $paramClass);
    }
}
