<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\ConnectContext\Traits\TConnectContextRelease;
use Imi\Server\Protocol;

/**
 * Close事件后置处理.
 *
 * @Listener("IMI.WORKERMAN.SERVER.CLOSE")
 */
class AfterClose implements IEventListener
{
    use TConnectContextRelease;

    /**
     * 事件处理方法.
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
