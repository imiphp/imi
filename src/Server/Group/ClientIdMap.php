<?php

declare(strict_types=1);

namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;

/**
 * clientId与所有服务器的分组关系.
 */
#[Bean(name: 'ClientIdMap')]
class ClientIdMap
{
    /**
     * 关联关系.
     */
    protected array $map = [];

    /**
     * 获取clientId绑定的所有的组.
     *
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups(int|string $clientId): array
    {
        return $this->map[$clientId] ?? [];
    }

    /**
     * 增加clientId关联关系.
     */
    public function joinGroup(int|string $clientId, Group $group): void
    {
        $map = &$this->map;
        if (!\in_array($group, $map[$clientId] ?? []))
        {
            $map[$clientId][] = $group;
        }
    }

    /**
     * 移除clientId关联关系.
     */
    public function leaveGroup(int|string $clientId, Group $group): void
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
     */
    public function leaveAll(int|string $clientId): void
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
