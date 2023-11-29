<?php

declare(strict_types=1);

namespace Imi\Grpc\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;

#[Listener(eventName: 'IMI.MAIN_SERVER.WORKER.START', priority: \Imi\Util\ImiPriority::IMI_MIN, one: true)]
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
