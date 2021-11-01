<?php

declare(strict_types=1);

namespace Imi\Workerman\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

/**
 * @Listener(eventName="IMI.WORKERMAN.SERVER.WORKER_START", priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class OnWorkerStart implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        App::getApp()->loadConfig();
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
