<?php

declare(strict_types=1);

namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;

/**
 * clientId与所有服务器的分组关系.
 *
 * @Bean("ClientIdMap")
 */
class ClientIdMap
{
    /**
     * 关联关系.
     */
    protected array $map = [];

    /**
     * 获取clientId绑定的所有的组.
     *
     * @param int|string $clientId
     *
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups($clientId): array
    {
        return $this->map[$clientId] ?? [];
    }

    /**
     * 增加clientId关联关系.
     *
     * @param int|string $clientId
     */
    public function joinGroup($clientId, Group $group): void
    {
        $map = &$this->map;
        $index = array_search($group, $map[$clientId] ?? []);
        if (false === $index)
        {
            $map[$clientId][] = $group;
        }
    }

    /**
     * 移除clientId关联关系.
     *
     * @param int|string $clientId
     */
    public function leaveGroup($clientId, Group $group): void
    {
        $map = &$this->map;
        $index = array_search($group, $map[$clientId] ?? []);
        if (false !== $index)
        {
            unset($map[$clientId][$index]);
        }
    }

    /**
     * 将clientId从所有组中移除.
     *
     * @param int|string $clientId
     */
    public function leaveAll($clientId): void
    {
        $map = $this->map[$clientId] ?? [];
        if ($map)
        {
            foreach ($map as $group)
            {
                $group->leave($clientId);
            }
        }
    }
}
