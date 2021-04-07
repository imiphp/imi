<?php

declare(strict_types=1);

namespace Imi\Server\Group\Handler;

interface IGroupHandler
{
    /**
     * 组是否存在.
     */
    public function hasGroup(string $groupName): bool;

    /**
     * 创建组，返回组对象
     */
    public function createGroup(string $groupName, int $maxClients = -1): void;

    /**
     * 加入组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function joinGroup(string $groupName, $clientId): bool;

    /**
     * 离开组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function leaveGroup(string $groupName, $clientId): bool;

    /**
     * 连接是否存在于组里.
     *
     * @param int|string $clientId
     */
    public function isInGroup(string $groupName, $clientId): bool;

    /**
     * 获取所有连接ID.
     *
     * @return int[]|string[]
     */
    public function getClientIds(string $groupName): array;

    /**
     * 获取在组中的连接总数.
     */
    public function count(string $groupName): int;

    /**
     * 清空分组.
     */
    public function clear(): void;
}
