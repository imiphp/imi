<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ConnectContext\ConnectionBinder;
use Imi\Workerman\Server\Server;

/**
 * 发送给标记对应的连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawByFlagRequest")
 */
class OnSendRawByFlagRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        ['data' => $data, 'flag' => $flag, 'serverName' => $serverName] = $data['data'];

        /** @var ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $fds = [];
        foreach ((array) $flag as $tmpFlag)
        {
            $fd = $connectionBinder->getFdByFlag($tmpFlag);
            if ($fd)
            {
                $fds[] = $fd;
            }
        }
        if (!$fds)
        {
            return;
        }
        Server::sendRaw($data, $fds, $serverName, false);
    }
}
