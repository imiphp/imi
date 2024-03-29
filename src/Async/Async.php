<?php

declare(strict_types=1);

namespace Imi\Async;

use Imi\Async\Contract\IAsyncHandler;
use Imi\Async\Contract\IAsyncResult;
use Imi\Async\Sync\SyncHandler;
use Imi\Config;

class Async
{
    use \Imi\Util\Traits\TStaticClass;

    private static IAsyncHandler $handler;

    /**
     * 获取实例.
     */
    public static function getInstance(): IAsyncHandler
    {
        if (!isset(self::$handler))
        {
            $handlerClass = Config::get('@app.imi.Async', SyncHandler::class);

            return self::$handler = new $handlerClass();
        }

        return self::$handler;
    }

    /**
     * 执行异步.
     */
    public static function exec(callable $callable): IAsyncResult
    {
        return self::getInstance()->exec($callable);
    }

    /**
     * 延后执行.
     */
    public static function defer(callable $callable): IAsyncResult
    {
        return self::getInstance()->defer($callable);
    }

    /**
     * 延后异步执行.
     */
    public static function deferAsync(callable $callable): IAsyncResult
    {
        return self::getInstance()->deferAsync($callable);
    }
}
