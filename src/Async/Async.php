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
}
