<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\ConnectionContext\Traits\TConnectionContextRelease;
use Imi\Server\Protocol;

/**
 * Close事件后置处理.
 */
#[Listener(eventName: 'IMI.WORKERMAN.SERVER.CLOSE', priority: (-9223372036854775807 - 1))]
class AfterClose implements IEventListener
{
    use TConnectionContextRelease;

    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        if (!\in_array(RequestContext::getServer()->getProtocol(), Protocol::LONG_CONNECTION_PROTOCOLS))
        {
            return;
        }
        ['clientId' => $clientId] = $e->getData();
        $this->release($clientId);
    }
}
