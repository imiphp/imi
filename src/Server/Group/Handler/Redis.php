<?php
namespace Imi\Server\Group\Handler;

use Imi\Util\ArrayUtil;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Bean;
use Swoole\Coroutine\Redis as CoRedis;
use Imi\RequestContext;

/**
 * @Bean("GroupRedis")
 */
class Redis implements IGroupHandler
{
	/**
	 * Redis 连接池名称
	 *
	 * @var string
	 */
	protected $redisPool;

	/**
	 * 心跳时间，单位：秒
	 *
	 * @var int
	 */
	protected $heartbeatTimespan = 5;

	/**
	 * 心跳数据过期时间，单位：秒
	 *
	 * @var int
	 */
	protected $heartbeatTtl = 8;

	/**
	 * 该服务的分组键
	 * 
	 * @var string
	 */
	protected $key = 'IMI.GROUP.KEY';

	/**
	 * 心跳Timer的ID
	 *
	 * @var int
	 */
	private $timerID;

	/**
	 * 组配置
	 *
	 * @var array
	 */
	private $groups = [];

	/**
	 * 主进程 ID
	 * @var int
	 */
	private $masterPID;

	public function __init()
	{
		PoolManager::use($this->redisPool, function($resource, $redis){
			// 判断master进程pid
			$this->masterPID = RequestContext::getServer()->getSwooleServer()->master_pid;
			$storeMasterPID = $redis->get($this->key);
			if(null !== $storeMasterPID && $this->masterPID != $storeMasterPID)
			{
				throw new \RuntimeException('Server Group Redis repeat');
			}
			if($redis->setnx($this->key, RequestContext::getServer()->getSwooleServer()->master_pid) && $redis->expire($this->key, $this->heartbeatTtl))
			{
				// 初始化所有分组列表
				$keys = $redis->keys($this->key . '-*');
				foreach($keys as $key)
				{
					try{
						if($redis->scard($key) > 0)
						{
							$redis->del($key);
						}
					}
					catch(\Throwable $ex)
					{

					}
				}
			}
		});
		// 心跳定时器
		$this->timerID = \swoole_timer_tick($this->heartbeatTimespan * 1000, [$this, 'timer']);
	}

	public function timer()
	{
		PoolManager::use($this->redisPool, function($resource, $redis){
			$redis->setex($this->key, $this->heartbeatTtl, $this->masterPID);
		});
	}

	public function __destruct()
	{
		if(null !== $this->timerID)
		{
			\swoole_timer_clear($this->timerID);
		}
	}

	/**
	 * 组是否存在
	 *
	 * @param string $groupName
	 * @return boolean
	 */
	public function hasGroup(string $groupName)
	{
		return true;
	}

	/**
	 * 创建组，返回组对象
	 *
	 * @param string $groupName
	 * @param integer $maxClients
	 * @return void
	 */
	public function createGroup(string $groupName, int $maxClients = -1)
	{
		if(!isset($this->groups[$groupName]))
		{
			$this->groups[$groupName] = [
				'maxClient'		=>	$maxClients,
			];
		}
	}

	/**
	 * 关闭组
	 *
	 * @param string $groupName
	 * @return void
	 */
	public function closeGroup(string $groupName)
	{
		PoolManager::use($this->redisPool, function($resource, $redis) use($groupName){
			$key = $this->getGroupNameKey($groupName);
			try{
				if($redis->scard($key) > 0)
				{
					$redis->del($key);
				}
			}
			catch(\Throwable $ex)
			{

			}
		});
	}

	/**
	 * 加入组，组不存在则自动创建
	 *
	 * @param string $groupName
	 * @param integer $fd
	 * @return void
	 */
	public function joinGroup(string $groupName, int $fd): bool
	{
		return PoolManager::use($this->redisPool, function($resource, $redis) use($groupName, $fd){
			$key = $this->getGroupNameKey($groupName);
			return $redis->sadd($key, $fd) > 0;
		});
	}

	/**
	 * 离开组，组不存在则自动创建
	 *
	 * @param string $groupName
	 * @param integer $fd
	 * @return void
	 */
	public function leaveGroup(string $groupName, int $fd): bool
	{
		return PoolManager::use($this->redisPool, function($resource, $redis) use($groupName, $fd){
			$key = $this->getGroupNameKey($groupName);
			return $redis->srem($key, $fd) > 0;
		});
	}

	/**
	 * 连接是否存在于组里
	 *
	 * @param string $groupName
	 * @param integer $fd
	 * @return boolean
	 */
	public function isInGroup(string $groupName, int $fd): bool
	{
		return PoolManager::use($this->redisPool, function($resource, $redis) use($groupName, $fd){
			$key = $this->getGroupNameKey($groupName);
			$redis->sIsMember($key, $fd);
		});
	}

	/**
	 * 获取所有fd
	 *
	 * @param string $groupName
	 * @return int[]
	 */
	public function getFds(string $groupName): array
	{
		return PoolManager::use($this->redisPool, function($resource, $redis) use($groupName){
			$key = $this->getGroupNameKey($groupName);
			if($this->groups[$groupName]['maxClient'] > 0)
			{
				return $redis->sRandMember($key, $this->groups[$groupName]['maxClient']);
			}
			else
			{
				return $redis->sMembers($key);
			}
		});
	}

	/**
	 * 获取组名处理后的键名
	 *
	 * @param string $groupName
	 * @return string
	 */
	public function getGroupNameKey(string $groupName): string
	{
		return $this->key . '-' . $groupName;
	}
}