<?php

declare(strict_types=1);

namespace Imi\Server\Group\Contract;

use Imi\Server\Group\Group;

interface IServerGroup
{
    /**
     * 组是否存在.
     */
    public function hasGroup(string $groupName): bool;

    /**
     * 创建组，返回组对象
     */
    public function createGroup(string $groupName, int $maxClients = -1): Group;

    /**
     * 获取组对象，不存在返回null.
     */
    public function getGroup(string $groupName): ?Group;

    /**
     * 加入组，组不存在则自动创建.
     */
    public function joinGroup(string $groupName, int $fd): void;

    /**
     * 离开组，组不存在则自动创建.
     */
    public function leaveGroup(string $groupName, int $fd): void;

    /**
     * 调用组方法.
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function groupCall(string $groupName, string $methodName, ...$args);

    /**
     * 获取所有组列表.
     *
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups(): array;
}
