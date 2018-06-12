<?php
namespace Imi\Redis;

use Imi\Pool\BasePoolResource;
use Imi\Pool\Interfaces\IPoolResource;

class CoroutineRedisResource extends BasePoolResource
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
		$result = $this->redis->connect($this->config['host'] ?? '127.0.0.1', $this->config['port'] ?? 6379, $this->config['serialize'] ?? true);
		if(isset($this->config['password']))
		{
			$result = $result && $this->redis->auth($this->config['password']);
		}
		if(isset($this->config['db']))
		{
			var_dump($this->config['db'], $result);
			$r = $this->redis->select($this->config['db']);
			$result = $result && $r;
			var_dump($r, $result);
		}
		return $result;
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