<?php

declare(strict_types=1);

namespace Imi\Server\Group;

trait TServerGroup
{
    /**
     * 组配置.
     *
     * @var \Imi\Server\Group\Group[]
     */
    private array $groups = [];

    /**
     * 组是否存在.
     */
    public function hasGroup(string $groupName): bool
    {
        if (!isset($this->groups[$groupName]))
        {
            /** @var \Imi\Server\Group\Group $serverGroup */
            $serverGroup = $this->getBean('ServerGroup', $this, $groupName);

            return $serverGroup->getHandler()->hasGroup($groupName);
        }

        return true;
    }

    /**
     * 创建组，返回组对象
     *
     * @return \Imi\Server\Group\Group
     */
    public function createGroup(string $groupName, int $maxClients = -1): Group
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $groups[$groupName] = $this->getBean('ServerGroup', $this, $groupName, $maxClients);
        }

        return $groups[$groupName];
    }

    /**
     * 获取组对象，不存在返回null.
     *
     * @return \Imi\Server\Group\Group|null
     */
    public function getGroup(string $groupName): ?Group
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            /** @var \Imi\Server\Group\Group $serverGroup */
            $serverGroup = $this->getBean('ServerGroup', $this, $groupName);

            if ($serverGroup->getHandler()->hasGroup($groupName))
            {
                return $groups[$groupName] = $serverGroup;
            }
        }

        return $groups[$groupName];
    }

    /**
     * 加入组，组不存在则自动创建.
     */
    public function joinGroup(string $groupName, int $fd): void
    {
        $this->createGroup($groupName)->join($fd);
    }

    /**
     * 离开组，组不存在则自动创建.
     */
    public function leaveGroup(string $groupName, int $fd): void
    {
        $this->createGroup($groupName)->leave($fd);
    }

    /**
     * 调用组方法.
     *
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function groupCall(string $groupName, string $methodName, ...$args)
    {
        return $this->createGroup($groupName)->$methodName(...$args);
    }

    /**
     * 获取所有组列表.
     *
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
