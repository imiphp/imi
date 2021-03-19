<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\ConnectContext\ConnectionBinder;
use Imi\Server\Server;

/**
 * 发送给分组中的连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.closeByFlagRequest")
 */
class OnCloseByFlagRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        ['flag' => $flag, 'serverName' => $serverName] = $data['data'];

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
        Server::close($fds, $serverName, false);
    }
}
