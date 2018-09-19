<?php
namespace Imi\Server\Group;

use Imi\Server\Group\Group;
use Imi\Bean\Annotation\Bean;

/**
 * fd与所有服务器的分组关系
 * 
 * @Bean("FdMap")
 */
class FdMap
{
    /**
     * 关联关系
     *
     * @var array
     */
    protected $map = [];

    /**
     * 获取fd绑定的所有的组
     *
     * @param integer $fd
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups(int $fd)
    {
        return $this->map[$fd] ?? [];
    }

    /**
     * 增加fd关联关系
     *
     * @param integer $fd
     * @param Group $group
     * @return void
     */
    public function joinGroup(int $fd, Group $group)
    {
        $index = array_search($group, $this->map[$fd] ?? []);
        if(false === $index)
        {
            $this->map[$fd][] = $group;
        }
    }

    /**
     * 移除fd关联关系
     *
     * @param integer $fd
     * @param Group $group
     * @return void
     */
    public function leaveGroup(int $fd, Group $group)
    {
        $index = array_search($group, $this->map[$fd] ?? []);
        if(false !== $index)
        {
            unset($this->map[$fd][$index]);
        }
    }

    /**
     * 将fd从所有组中移除
     *
     * @param integer $fd
     * @return void
     */
    public function leaveAll(int $fd)
    {
        $map = $this->map[$fd] ?? [];
        foreach($map as $group)
        {
            $group->leave($fd);
        }
    }
}