<?php

declare(strict_types=1);

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
    private RedisHandler $redis;

    /**
     * 连接配置.
     */
    private array $config = [];

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, RedisHandler $redis, array $config)
    {
        parent::__construct($pool);
        $this->redis = $redis;
        $this->config = $config;
    }

    /**
     * 打开
     */
    public function open(): bool
    {
        RedisManager::initRedisConnection($this->redis, $this->config);

        return true;
    }

    /**
     * 关闭.
     */
    public function close(): void
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
     */
    public function reset(): void
    {
        $config = $this->config;
        $redis = $this->redis;
        if ($redis->isConnected())
        {
            if (!$redis->select($config['db'] ?? 0))
            {
                throw new \RedisException('Redis select db failed');
            }
            $optScan = $config['options'][\Redis::OPT_SCAN] ?? \Redis::SCAN_RETRY;
            if (!$redis->setOption(\Redis::OPT_SCAN, $optScan))
            {
                throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', \Redis::OPT_SCAN, $optScan));
            }
        }
    }

    /**
     * 检查资源是否可用.
     */
    public function checkState(): bool
    {
        try
        {
            $result = $this->redis->ping();
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
