<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectionCenter\Facade\ConnectionCenter;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;

#[Listener(eventName: 'IMI.COMMAND.AFTER')]
class CloseAllListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        ConnectionCenter::closeAllConnectionManager();
    }
}
