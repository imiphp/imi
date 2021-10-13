<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Server\Group;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Group\Handler\IGroupHandler;

/**
 * 分组 Workerman Gateway 驱动.
 *
 * @Bean("GroupGateway")
 */
class GatewayGroupHandler implements IGroupHandler
{
    /**
     * {@inheritDoc}
     */
    public function startup(): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function hasGroup(string $groupName): bool
    {
        return Gateway::getClientCountByGroup($groupName) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function createGroup(string $groupName, int $maxClients = -1): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function closeGroup(string $groupName): void
    {
        Gateway::ungroup($groupName);
    }

    /**
     * {@inheritDoc}
     */
    public function joinGroup(string $groupName, $clientId): bool
    {
        Gateway::joinGroup($clientId, $groupName);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function leaveGroup(string $groupName, $clientId): bool
    {
        Gateway::leaveGroup($clientId, $groupName);

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isInGroup(string $groupName, $clientId): bool
    {
        return isset(Gateway::getClientIdListByGroup($groupName)[$clientId]);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIds(string $groupName): array
    {
        return Gateway::getClientIdListByGroup($groupName);
    }

    /**
     * {@inheritDoc}
     */
    public function count(string $groupName): int
    {
        return Gateway::getClientCountByGroup($groupName);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        foreach (Gateway::getAllGroupIdList() as $groupName)
        {
            Gateway::ungroup($groupName);
        }
    }
}
