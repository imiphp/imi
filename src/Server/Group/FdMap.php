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

	public function getGroups(int $fd)
	{
		return $this->map[$fd] ?? [];
	}

	public function joinGroup(int $fd, Group $group)
	{
		$index = array_search($group, $this->map[$fd] ?? []);
		if(false === $index)
		{
			$this->map[$fd][] = $group;
		}
	}

	public function leaveGroup(int $fd, Group $group)
	{
		$index = array_search($group, $this->map[$fd] ?? []);
		if(false !== $index)
		{
			unset($this->map[$fd][$index]);
		}
	}

	public function leaveAll(int $fd)
	{
		$map = $this->map[$fd] ?? [];
		foreach($map as $group)
		{
			$group->leave($fd);
		}
	}
}