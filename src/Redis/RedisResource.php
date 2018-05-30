<?php
namespace Imi\Redis;

use Imi\Pool\BasePoolResource;
use Imi\Pool\Interfaces\IPoolResource;

class RedisResource extends BasePoolResource
{
	/**
	 * db对象
	 * @var \Swoole\Coroutine\Redis
	 */
	private $redis;

	/**
	 * 连接配置
	 * @var array
	 */
	private $config;

	public function __construct(\Imi\Pool\Interfaces\IPool $pool, \Swoole\Coroutine\Redis $redis, $config)
	{
		parent::__construct($pool);
		$this->redis = $redis;
		$this->config = $config;
	}

	/**
	 * 打开
	 * @return boolean
	 */
	public function open($callback = null)
	{
		$this->redis->connect($config['host'] ?? '127.0.0.1', $config['port'] ?? 6379, $config['serialize'] ?? true);
	}

	/**
	 * 关闭
	 * @return void
	 */
	public function close()
	{
		$this->redis->close();
	}

	/**
	 * 获取对象实例
	 * @return mixed
	 */
	public function getInstance()
	{
		return $this->redis;
	}
}