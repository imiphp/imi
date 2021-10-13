<?php

declare(strict_types=1);

namespace Imi\Server\Group\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\ConnectionContext;
use Imi\Server\ConnectionContext\Event\Listener\IConnectionContextRestoreListener;
use Imi\Server\ConnectionContext\Event\Param\ConnectionContextRestoreParam;
use Imi\Server\Group\Contract\IServerGroup;
use Imi\Server\ServerManager;

/**
 * @Listener(eventName="IMI.CONNECT_CONTEXT.RESTORE")
 */
class GroupRestore implements IConnectionContextRestoreListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(ConnectionContextRestoreParam $e): void
    {
        $fromClientId = $e->fromClientId;
        $toClientId = $e->toClientId;
        $serverName = $e->serverName;
        $connectionContextData = ConnectionContext::getContext($fromClientId, $serverName);
        $groups = $connectionContextData['__groups'] ?? [];
        if (!$groups)
        {
            return;
        }
        /** @var IServerGroup $server */
        $server = ServerManager::getServer($serverName ?? $connectionContextData['__serverName'], IServerGroup::class);
        foreach ($groups as $group)
        {
            $server->joinGroup($group, $toClientId);
        }
    }
}
