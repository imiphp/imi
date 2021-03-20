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
     *
     * @var array
     */
    private $groups = [];

    /**
     * 组是否存在.
     *
     * @param string $groupName
     *
     * @return bool
     */
    public function hasGroup(string $groupName)
    {
        return isset($this->groups[$groupName]);
    }

    /**
     * 创建组，返回组对象
     *
     * @param string $groupName
     * @param int    $maxClients
     *
     * @return void
     */
    public function createGroup(string $groupName, int $maxClients = -1)
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
     *
     * @param string $groupName
     *
     * @return void
     */
    public function closeGroup(string $groupName)
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
     * @param string $groupName
     * @param int    $fd
     *
     * @return bool
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
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return bool
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
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return bool
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
     * @param string $groupName
     *
     * @return int[]
     */
    public function getFds(string $groupName): array
    {
        return \array_values($this->groups[$groupName]['fds'] ?? []);
    }

    /**
     * 获取组中的连接总数.
     *
     * @return int
     */
    public function count(string $groupName): int
    {
        return \count($this->groups[$groupName]['fds'] ?? []);
    }

    /**
     * 清空分组.
     *
     * @return void
     */
    public function clear()
    {
        $this->groups = [];
    }
}
