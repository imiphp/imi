<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Imi\Workerman\Server\Server;

/**
 * 发送给分组中的连接-请求
 *
 * @Listener(eventName="IMI.PIPE_MESSAGE.sendRawToGroupRequest")
 */
class OnSendRawToGroupRequest implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $data = $e->getData();
        ['data' => $data, 'groupName' => $groupName, 'serverName' => $serverName] = $data['data'];

        /** @var IWorkermanServer|null $server */
        $server = Server::getServer($serverName);
        if (!$server)
        {
            return;
        }

        $groups = (array) $groupName;
        foreach ($groups as $tmpGroupName)
        {
            $group = $server->getGroup($tmpGroupName);
            if ($group)
            {
                Server::sendRaw($data, $group->getFds(), $serverName, false);
            }
        }
    }
}
