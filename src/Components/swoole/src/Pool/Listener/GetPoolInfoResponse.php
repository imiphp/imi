<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Util\Co\ChannelContainer;

/**
 * 获取指定连接池信息-响应.
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.getPoolInfoResponse")
 */
class GetPoolInfoResponse implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData()['data'];
        if (ChannelContainer::hasChannel($data['messageId']))
        {
            ChannelContainer::push($data['messageId'], $data);
        }
    }
}
