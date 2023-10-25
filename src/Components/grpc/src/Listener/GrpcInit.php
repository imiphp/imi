<?php

declare(strict_types=1);

namespace Imi\Grpc\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

#[Listener(eventName: 'IMI.MAIN_SERVER.WORKER.START', priority: -19940312, one: true)]
class GrpcInit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        App::getBean('GrpcInterfaceManager');
    }
}
