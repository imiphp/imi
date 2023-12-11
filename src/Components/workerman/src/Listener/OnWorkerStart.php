<?php

declare(strict_types=1);

namespace Imi\Workerman\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Util\Imi;

#[Listener(eventName: WorkermanEvents::SERVER_WORKER_START, priority: \Imi\Util\ImiPriority::IMI_MAX, one: true)]
class OnWorkerStart implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        Imi::setProcessName('worker');
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
