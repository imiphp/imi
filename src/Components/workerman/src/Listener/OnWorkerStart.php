<?php

declare(strict_types=1);

namespace Imi\Workerman\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener("IMI.WORKERMAN.SERVER.WORKER_START")
 */
class OnWorkerStart implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        foreach (Config::get('@app.db.connections', []) as $name => $_)
        {
            App::set('__db.' . $name, null);
        }
        foreach (Config::get('@app.redis.connections', []) as $name => $_)
        {
            App::set('__redis.' . $name, null);
        }
    }
}
