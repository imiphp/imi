<?php
namespace Imi\Server\ConnectContext\StoreHandler;

use Imi\RequestContext;
use Imi\Pool\PoolManager;
use Imi\Bean\Annotation\Bean;

/**
 * 连接上下文存储处理器-Redis
 * @Bean("ConnectContextRedis")
 */
class Redis implements IHandler
{
	/**
	 * Redis 连接池名称
	 *
	 * @var string
	 */
	protected $redisPool;

	/**
	 * redis中第几个库
	 *
	 * @var integer
	 */
	protected $redisDb = 0;

	/**
	 * 键
	 * 
	 * @var string
	 */
	protected $key = 'IMI.CONNECT.CONTEXT';

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
	 * 心跳Timer的ID
	 *
	 * @var int
	 */
	private $timerID;

	/**
	 * 主进程 ID
	 * @var int
	 */
	private $masterPID;

	public function __init()
	{
		if(null === $this->redisPool)
		{
			return;
		}
		$this->useRedis(function($resource, $redis){
			// 判断master进程pid
			$this->masterPID = RequestContext::getServer()->getSwooleServer()->master_pid;
			$hasPing = $this->hasPing($redis);
			$storeMasterPID = $redis->get($this->key);
			if(null === $storeMasterPID)
			{
				// 没有存储master进程pid
				$this->initRedis($redis, $storeMasterPID);
			}
			else if($this->masterPID != $storeMasterPID)
			{
				if($hasPing)
				{
					// 与master进程ID不等
					throw new \RuntimeException('ConnectContextRedis repeat');
				}
				else
				{
					$this->initRedis($redis, $storeMasterPID);
				}
			}
			$this->startPing($redis);
		});
	}

	/**
	 * 初始化redis数据
	 *
	 * @param mixed $redis
	 * @param int $storeMasterPID
	 * @return void
	 */
	private function initRedis($redis, $storeMasterPID = null)
	{
		if(null !== $storeMasterPID && $redis->del($this->key))
		{
			return;
		}
		if($redis->setnx($this->key, $this->masterPID))
		{
			// 初始化所有分组列表
			$keys = $redis->keys($this->key . '.*');
			foreach($keys as $key)
			{
				$redis->del($key);
			}
		}
	}

	/**
	 * 开始ping
	 *
	 * @param mixed $redis
	 * @return void
	 */
	private function startPing($redis)
	{
		if($this->ping($redis))
		{
			// 心跳定时器
			$this->timerID = \swoole_timer_tick($this->heartbeatTimespan * 1000, [$this, 'pingTimer']);
		}
	}

	/**
	 * ping定时器执行操作
	 *
	 * @return void
	 */
	public function pingTimer()
	{
		$this->useRedis(function($resource, $redis){
			$this->ping($redis);
		});
	}

	/**
	 * 获取redis中存储ping的key
	 *
	 * @return void
	 */
	private function getPingKey()
	{
		return $this->key . '-PING';
	}

	/**
	 * ping操作
	 *
	 * @param mixed $redis
	 * @return boolean
	 */
	private function ping($redis)
	{
		$key = $this->getPingKey();
		$redis->multi();
		$redis->set($key, '');
		$redis->expire($key, $this->heartbeatTtl);
		$result = $redis->exec();
		foreach($result as $value)
		{
			if(!$value)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * 是否有ping
	 *
	 * @param mixed $redis
	 * @return boolean
	 */
	private function hasPing($redis)
	{
		$key = $this->getPingKey();
		return 1 == $redis->exists($key);
	}

	public function __destruct()
	{
		if(null !== $this->timerID)
		{
			\swoole_timer_clear($this->timerID);
		}
	}

	/**
	 * 读取数据
	 *
	 * @param string $key
	 * @return array
	 */
	public function read(string $key): array
	{
		return $this->useRedis(function($resource, $redis) use($key){
			$redisKey = $this->getRedisKey($key);
			return $redis->get($redisKey) ?? [];
		});
	}

	/**
	 * 保存数据
	 *
	 * @param string $key
	 * @param array $data
	 * @return void
	 */
	public function save(string $key, array $data)
	{
		$this->useRedis(function($resource, $redis) use($key, $data){
			$redisKey = $this->getRedisKey($key);
			$redis->set($redisKey, $data);
		});
	}

	/**
	 * 销毁数据
	 *
	 * @param string $key
	 * @return void
	 */
	public function destroy(string $key)
	{
		$this->useRedis(function($resource, $redis) use($key){
			$redisKey = $this->getRedisKey($key);
			$redis->del($redisKey);
		});
	}

	/**
	 * 数据是否存在
	 *
	 * @param string $key
	 * @return void
	 */
	public function exists(string $key)
	{
		return $this->useRedis(function($resource, $redis) use($key){
			$redisKey = $this->getRedisKey($key);
			return $redis->exists($redisKey);
		});
	}

	/**
	 * 获取组名处理后的键名
	 *
	 * @param string $groupName
	 * @return string
	 */
	private function getRedisKey(string $key): string
	{
		return $this->key . '.' . $this->masterPID . '.' . $key;
	}

	/**
	 * 使用redis
	 *
	 * @param callable $callback
	 * @return void
	 */
	private function useRedis($callback)
	{
		return PoolManager::use($this->redisPool, function($resource, $redis) use($callback){
			$redis->select($this->redisDb);
			return $callback($resource, $redis);
		});
	}

}