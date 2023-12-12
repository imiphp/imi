<?php

declare(strict_types=1);

namespace Imi\Grpc\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Server\Event\ServerEvents;

#[Listener(eventName: ServerEvents::WORKER_START, priority: \Imi\Util\ImiPriority::IMI_MIN, one: true)]
class GrpcInit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        App::getBean('GrpcInterfaceManager');
    }
}
