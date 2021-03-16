<?php

declare(strict_types=1);

namespace Imi\Server\Group;

use Imi\Bean\Annotation\Bean;

/**
 * fd与所有服务器的分组关系.
 *
 * @Bean("FdMap")
 */
class FdMap
{
    /**
     * 关联关系.
     */
    protected array $map = [];

    /**
     * 获取fd绑定的所有的组.
     *
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups(int $fd): array
    {
        return $this->map[$fd] ?? [];
    }

    /**
     * 增加fd关联关系.
     */
    public function joinGroup(int $fd, Group $group): void
    {
        $map = &$this->map;
        $index = array_search($group, $map[$fd] ?? []);
        if (false === $index)
        {
            $map[$fd][] = $group;
        }
    }

    /**
     * 移除fd关联关系.
     */
    public function leaveGroup(int $fd, Group $group): void
    {
        $map = &$this->map;
        $index = array_search($group, $map[$fd] ?? []);
        if (false !== $index)
        {
            unset($map[$fd][$index]);
        }
    }

    /**
     * 将fd从所有组中移除.
     */
    public function leaveAll(int $fd): void
    {
        $map = $this->map[$fd] ?? [];
        foreach ($map as $group)
        {
            $group->leave($fd);
        }
    }
}
