<?php

declare(strict_types=1);

namespace Imi\Async;

use Imi\Async\Contract\IAsyncHandler;
use Imi\Async\Contract\IAsyncResult;
use Imi\Async\Sync\SyncHandler;
use Imi\Config;

class Async
{
    private static IAsyncHandler $handler;

    private function __construct()
    {
    }

    /**
     * 获取实例.
     */
    public static function getInstance(): IAsyncHandler
    {
        if (!isset(static::$handler))
        {
            $handlerClass = Config::get('@app.imi.Async', SyncHandler::class);

            return static::$handler = new $handlerClass();
        }

        return static::$handler;
    }

    /**
     * 执行异步.
     */
    public static function exec(callable $callable): IAsyncResult
    {
        return static::getInstance()->exec($callable);
    }
}
