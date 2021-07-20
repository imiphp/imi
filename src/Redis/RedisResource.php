<?php

namespace Imi\Redis;

use Imi\App;
use Imi\Pool\BasePoolResource;

class RedisResource extends BasePoolResource
{
    /**
     * Redis 对象
     *
     * @var \Imi\Redis\RedisHandler
     */
    private $redis;

    /**
     * 连接配置.
     *
     * @var array
     */
    private $config;

    /**
     * @param \Imi\Pool\Interfaces\IPool $pool
     * @param RedisHandler               $redis
     * @param array                      $config
     */
    public function __construct(\Imi\Pool\Interfaces\IPool $pool, RedisHandler $redis, $config)
    {
        parent::__construct($pool);
        $this->redis = $redis;
        $this->config = $config;
    }

    /**
     * 打开
     *
     * @return bool
     */
    public function open()
    {
        $redis = $this->redis;
        $config = $this->config;
        if (!$redis->isCluster())
        {
            $redis->connect($config['host'] ?? '127.0.0.1', $config['port'] ?? 6379, $config['timeout'] ?? null);
            if (('' !== ($config['password'] ?? '')) && !$redis->auth($config['password']))
            {
                throw new \RedisException('Redis auth failed');
            }
            if (isset($config['db']) && !$redis->select($config['db']))
            {
                throw new \RedisException('Redis select db failed');
            }
        }
        $options = $config['options'] ?? [];
        if (($config['serialize'] ?? true) && !isset($options[\Redis::OPT_SERIALIZER]))
        {
            $options[\Redis::OPT_SERIALIZER] = \Redis::SERIALIZER_PHP;
        }
        if ($options)
        {
            foreach ($options as $key => $value)
            {
                if (!$redis->setOption($key, $value))
                {
                    throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', $key, $value));
                }
            }
        }

        return true;
    }

    /**
     * 关闭.
     *
     * @return void
     */
    public function close()
    {
        $this->redis->close();
    }

    /**
     * 获取对象实例.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->redis;
    }

    /**
     * 重置资源，当资源被使用后重置一些默认的设置.
     *
     * @return void
     */
    public function reset()
    {
        $config = $this->config;
        $redis = $this->redis;
        if (!$redis->isCluster() && $redis->isConnected() && !$redis->select($config['db'] ?? 0))
        {
            throw new \RedisException('Redis select db failed');
        }
        $optScan = $config['options'][\Redis::OPT_SCAN] ?? \Redis::SCAN_RETRY;
        if (!$redis->setOption(\Redis::OPT_SCAN, $optScan))
        {
            throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', \Redis::OPT_SCAN, $optScan));
        }
    }

    /**
     * 检查资源是否可用.
     *
     * @return bool
     */
    public function checkState(): bool
    {
        $redis = $this->redis;
        if ($redis->isCluster())
        {
            return true;
        }
        else
        {
            try
            {
                $result = $redis->ping();
                // PHPRedis 扩展，5.0.0 版本开始，ping() 返回为 true，旧版本为 +PONG
                return true === $result || '+PONG' === $result;
            }
            catch (\Throwable $ex)
            {
                /** @var \Imi\Log\ErrorLog $errorLog */
                $errorLog = App::getBean('ErrorLog');
                $errorLog->onException($ex);

                return false;
            }
        }
    }
}
