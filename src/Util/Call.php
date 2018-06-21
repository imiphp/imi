<?php

namespace Imi\Util;

use Imi\Util\Coroutine;

class Call
{
    /**
     * call_user_func，智能判断，优先使用协程
     * @param callable $callback
     * @param mixed ...$parameter
     * @return mixed
     */
    public static function callUserFunc($callback, ...$parameter)
    {
        if (swoole_version() < 3 && Coroutine::isIn()) {
            return Coroutine::call_user_func($callback, ...$parameter);
        } else {
            return call_user_func($callback, ...$parameter);
        }
    }


    /**
     * call_user_func_array，智能判断，优先使用协程
     * @param callable $callback
     * @param array $paramArray
     * @return mixed
     */
    public static function callUserFuncArray($callback, $paramArray)
    {
        if (swoole_version() < 3 && Coroutine::isIn()) {
            return Coroutine::call_user_func_array($callback, $paramArray);
        } else {
            return call_user_func_array($callback, $paramArray);
        }
    }
}