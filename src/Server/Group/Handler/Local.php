<?php

declare(strict_types=1);

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
                'maxClient'       => $maxClients,
                'clientIds'       => [],
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
     *
     * @param int|string $clientId
     */
    public function joinGroup(string $groupName, $clientId): bool
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $this->createGroup($groupName);
        }
        $groups[$groupName]['clientIds'][$clientId] = $clientId;

        return true;
    }

    /**
     * 离开组，组不存在则自动创建.
     *
     * @param int|string $clientId
     */
    public function leaveGroup(string $groupName, $clientId): bool
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            $clientIds = &$groups[$groupName]['clientIds'];
            if (isset($clientIds[$clientId]))
            {
                unset($clientIds[$clientId]);

                return true;
            }
        }

        return false;
    }

    /**
     * 连接是否存在于组里.
     *
     * @param int|string $clientId
     */
    public function isInGroup(string $groupName, $clientId): bool
    {
        $groups = &$this->groups;
        if (isset($groups[$groupName]))
        {
            return isset($groups[$groupName]['clientIds'][$clientId]);
        }

        return false;
    }

    /**
     * 获取所有连接ID.
     *
     * @return int[]|string[]
     */
    public function getClientIds(string $groupName): array
    {
        return array_values($this->groups[$groupName]['clientIds'] ?? []);
    }

    /**
     * 获取组中的连接总数.
     */
    public function count(string $groupName): int
    {
        return \count($this->groups[$groupName]['clientIds'] ?? []);
    }

    /**
     * 清空分组.
     */
    public function clear(): void
    {
        $this->groups = [];
    }
}
