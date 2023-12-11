<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\ConnectionContext\Traits\TConnectionContextRelease;
use Imi\Server\Protocol;
use Imi\Util\ImiPriority;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Http\Event\WorkermanConnectionCloseEvent;

/**
 * Close事件后置处理.
 */
#[Listener(eventName: WorkermanEvents::SERVER_CLOSE, priority: ImiPriority::MIN)]
class AfterClose implements IEventListener
{
    use TConnectionContextRelease;

    /**
     * @param WorkermanConnectionCloseEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if (!\in_array(RequestContext::getServer()->getProtocol(), Protocol::LONG_CONNECTION_PROTOCOLS))
        {
            return;
        }
        $this->release($e->clientId);
    }
}
