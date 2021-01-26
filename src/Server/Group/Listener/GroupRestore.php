<?php

declare(strict_types=1);

namespace Imi\Server\Group\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectContext;
use Imi\Server\ConnectContext\Event\Listener\IConnectContextRestoreListener;
use Imi\Server\ConnectContext\Event\Param\ConnectContextRestoreParam;
use Imi\Server\Group\Contract\IServerGroup;
use Imi\Server\ServerManager;

/**
 * @Listener(eventName="IMI.CONNECT_CONTEXT.RESTORE")
 */
class GroupRestore implements IConnectContextRestoreListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(ConnectContextRestoreParam $e)
    {
        $fromFd = $e->fromFd;
        $toFd = $e->toFd;
        $connectContextData = ConnectContext::getContext($fromFd);
        $groups = $connectContextData['__groups'] ?? [];
        if (!$groups)
        {
            return;
        }
        /** @var IServerGroup $server */
        $server = ServerManager::getServer($connectContextData['__serverName'], IServerGroup::class);
        foreach ($groups as $group)
        {
            $server->joinGroup($group, $toFd);
        }
    }
}
