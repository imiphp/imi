<?php

declare(strict_types=1);

namespace Imi\Db\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\ImiPriority;

/**
 * @Listener(eventName="IMI.APP_RUN", priority=ImiPriority::IMI_MAX)
 */
class WorkerStart implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        App::getBean('DbQueryLog');
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Db\Listener\RequestContextDestroy(), 'handle'], ImiPriority::IMI_MIN - 20);
    }
}
