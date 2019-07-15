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

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, RedisHandler $redis, $config)
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
            $r = $this->redis->select($this->config['db']);
            $result = $result && $r;
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
    
    /**
     * 重置资源，当资源被使用后重置一些默认的设置
     * @return void
     */
    public function reset()
    {
        $this->redis->setDefer(false);
        $this->redis->select($this->config['db'] ?? 0);
    }

    /**
     * 检查资源是否可用
     * @return bool
     */
    public function checkState(): bool
    {
        try{
            return 0 === $this->redis->ping();
        }catch(\Throwable $ex)
        {
            return false;
        }
    }
}