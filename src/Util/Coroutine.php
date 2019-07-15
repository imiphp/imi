<?php
namespace Imi\Util;

abstract class Coroutine extends \Swoole\Coroutine
{
    /**
     * 判断当前是否在协程中运行
     * @return boolean
     */
    public static function isIn()
    {
        return static::getuid() > -1;
    }
}