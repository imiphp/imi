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

        if (isset($config['timeout']))
        {
            $config['timeout'] = (float) $config['timeout'];
        }
        if (isset($config['db']))
        {
            $config['db'] = (int) $config['db'];
        }

        $this->redis = $redis;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function open(): bool
    {
        RedisManager::initRedisConnection($this->redis, $this->config);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->redis->close();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance()
    {
        return $this->redis;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $config = $this->config;
        $redis = $this->redis;
        if (!$redis->isCluster() && $redis->isConnected() && !$redis->select($config['db'] ?? 0))
        {
            throw new \RedisException($redis->getLastError());
        }
        $optScan = $config['options'][\Redis::OPT_SCAN] ?? \Redis::SCAN_RETRY;
        if (!$redis->setOption(\Redis::OPT_SCAN, $optScan))
        {
            throw new \RuntimeException(sprintf('Redis setOption %s=%s failed', \Redis::OPT_SCAN, $optScan));
        }
    }

    /**
     * {@inheritDoc}
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
