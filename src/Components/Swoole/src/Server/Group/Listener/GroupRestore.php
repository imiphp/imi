<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Group\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectContext;
use Imi\ServerManage;
use Imi\Swoole\Server\ConnectContext\Event\Listener\IConnectContextRestoreListener;
use Imi\Swoole\Server\ConnectContext\Event\Param\ConnectContextRestoreParam;

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
        $server = ServerManage::getServer($connectContextData['__serverName']);
        foreach ($groups as $group)
        {
            $server->joinGroup($group, $toFd);
        }
    }
}
