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
     * 启动时执行.
     */
    public function startup(): void
    {
    }

    /**
     * 组是否存在.
     */
    public function hasGroup(string $groupName): bool
    {
        return Gateway::getClientCountByGroup($groupName) > 0;
    }

    /**
     * 创建组，返回组对象
     */
    public function createGroup(string $groupName, int $maxClients = -1): void
    {
    }

    /**
     * 关闭组.
     */
    public function closeGroup(string $groupName): void
    {
        Gateway::ungroup($groupName);
    }

    /**
     * 加入组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function joinGroup(string $groupName, $clientId): bool
    {
        Gateway::joinGroup($clientId, $groupName);

        return true;
    }

    /**
     * 离开组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function leaveGroup(string $groupName, $clientId): bool
    {
        Gateway::leaveGroup($clientId, $groupName);

        return false;
    }

    /**
     * 连接是否存在于组里.
     *
     * @param int|string $clientId
     */
    public function isInGroup(string $groupName, $clientId): bool
    {
        return isset(Gateway::getClientIdListByGroup($groupName)[$clientId]);
    }

    /**
     * 获取所有连接ID.
     *
     * @return int[]|string[]
     */
    public function getClientIds(string $groupName): array
    {
        return Gateway::getClientIdListByGroup($groupName);
    }

    /**
     * 获取组中的连接总数.
     */
    public function count(string $groupName): int
    {
        return Gateway::getClientCountByGroup($groupName);
    }

    /**
     * 清空分组.
     */
    public function clear(): void
    {
        foreach (Gateway::getAllGroupIdList() as $groupName)
        {
            Gateway::ungroup($groupName);
        }
    }
}
