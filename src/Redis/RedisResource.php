<?php
namespace Imi\Redis;

use Imi\App;
use Imi\Pool\BasePoolResource;

class RedisResource extends BasePoolResource
{
    /**
     * Redis 对象
     * @var \Imi\Redis\RedisHandler
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
    public function open()
    {
        $this->redis->connect($this->config['host'] ?? '127.0.0.1', $this->config['port'] ?? 6379, $this->config['timeout'] ?? null);
        if(('' !== ($this->config['password'] ?? '')) && !$this->redis->auth($this->config['password']))
        {
            throw new \RedisException('Redis auth failed');
        }
        if(isset($this->config['db']) && !$this->redis->select($this->config['db']))
        {
            throw new \RedisException('Redis select db failed');
        }
        $options = $this->config['options'] ?? [];
        if(($this->config['serialize'] ?? true) && !isset($options[\Redis::OPT_SERIALIZER]))
        {
            $options[\Redis::OPT_SERIALIZER] = \Redis::SERIALIZER_PHP;
        }
        foreach($options as $key => $value)
        {
            if(!$this->redis->setOption($key, $value))
            {
                throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', $key, $value));
            }
        }
        return true;
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
        if(!$this->redis->select($this->config['db'] ?? 0))
        {
            throw new \RedisException('Redis select db failed');
        }
        $optScan = $this->config['options'][\Redis::OPT_SCAN] ?? \Redis::SCAN_RETRY;
        if(!$this->redis->setOption(\Redis::OPT_SCAN, $optScan))
        {
            throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', \Redis::OPT_SCAN, $optScan));
        }
    }
    
    /**
     * 检查资源是否可用
     * @return bool
     */
    public function checkState(): bool
    {
        try{
            $result = $this->redis->ping();
            // PHPRedis 扩展，5.0.0 版本开始，ping() 返回为 true，旧版本为 +PONG
            return true === $result || '+PONG' === $result;
        } catch(\Throwable $ex) {
            /** @var \Imi\Log\ErrorLog $errorLog */
            $errorLog = App::getBean('ErrorLog');
            $errorLog->onException($ex);
            return false;
        }
    }
}