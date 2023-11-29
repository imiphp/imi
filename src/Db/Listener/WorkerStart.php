<?php

declare(strict_types=1);

namespace Imi\Db\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\Event;
use Imi\Event\IEventListener;
use Imi\Util\ImiPriority;

#[Listener(eventName: 'IMI.APP_RUN', priority: \Imi\Util\ImiPriority::IMI_MAX, one: true)]
class WorkerStart implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        App::getBean('DbQueryLog');
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Db\Listener\RequestContextDestroy(), 'handle'], ImiPriority::IMI_MIN - 20);
    }
}
