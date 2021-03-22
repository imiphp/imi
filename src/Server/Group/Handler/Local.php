<?php

namespace Imi\Server\Group\Handler;

use Imi\Bean\Annotation\Bean;

/**
 * 分组本地驱动.
 *
 * @Bean("GroupLocal")
 */
class Local implements IGroupHandler
{
    /**
     * 组配置.
     */
    private array $groups = [];

    /**
     * 组是否存在.
     */
    public function hasGroup(string $groupName): bool
    {
        return isset($this->groups[$groupName]);
    }

    /**
     * 创建组，返回组对象
     */
    public function createGroup(string $groupName, int $maxClients = -1): void
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $groups[$groupName] = [
                'maxClient' => $maxClients,
                'fds'       => [],
            ];
        }
    }

    /**
     * 关闭组.
     */
    public function closeGroup(string $groupName): void
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            unset($groups[$groupName]);
        }
    }

    /**
     * 加入组，组不存在则自动创建.
     */
    public function joinGroup(string $groupName, int $fd): bool
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $this->createGroup($groupName);
        }
        $groups[$groupName]['fds'][$fd] = $fd;

        return true;
    }

    /**
     * 离开组，组不存在则自动创建.
     */
    public function leaveGroup(string $groupName, int $fd): bool
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            $fds = &$groups[$groupName]['fds'];
            if (isset($fds[$fd]))
            {
                unset($fds[$fd]);

                return true;
            }
        }

        return false;
    }

    /**
     * 连接是否存在于组里.
     */
    public function isInGroup(string $groupName, int $fd): bool
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            return isset($groups[$groupName]['fds'][$fd]);
        }

        return false;
    }

    /**
     * 获取所有fd.
     *
     * @return int[]
     */
    public function getFds(string $groupName): array
    {
        return array_values($this->groups[$groupName]['fds'] ?? []);
    }

    /**
     * 获取组中的连接总数.
     */
    public function count(string $groupName): int
    {
        return \count($this->groups[$groupName]['fds'] ?? []);
    }

    /**
     * 清空分组.
     */
    public function clear(): void
    {
        $this->groups = [];
    }
}
