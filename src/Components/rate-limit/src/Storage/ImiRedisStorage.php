<?php

declare(strict_types=1);

namespace Imi\RateLimit\Storage;

use bandwidthThrottle\tokenBucket\storage\scope\GlobalScope;
use bandwidthThrottle\tokenBucket\storage\Storage;
use bandwidthThrottle\tokenBucket\storage\StorageException;
use bandwidthThrottle\tokenBucket\util\DoublePacker;
use Imi\Redis\RedisHandler;
use malkusch\lock\mutex\Mutex;
use malkusch\lock\mutex\PHPRedisMutex;

/**
 * Imi Redis 存储器.
 */
final class ImiRedisStorage implements Storage, GlobalScope
{
    /**
     * The mutex.
     */
    private PHPRedisMutex $mutex;

    /**
     * The redis API.
     */
    private RedisHandler $redis;

    /**
     * The key.
     */
    private string $key = '';

    /**
     * Sets the connected Redis API.
     *
     * The Redis API needs to be connected yet. I.e. Redis::connect() was
     * called already.
     *
     * @param string                  $name  the resource name
     * @param \Imi\Redis\RedisHandler $redis the Redis API
     */
    public function __construct(string $name, RedisHandler $redis, int $timeout = 3)
    {
        $this->key = $name;
        $this->redis = $redis;
        // @phpstan-ignore-next-line
        $this->mutex = new PHPRedisMutex([$redis], $name, $timeout);
    }

    /**
     * @param float $microtime
     *
     * @return void
     */
    public function bootstrap($microtime)
    {
        $this->setMicrotime($microtime);
    }

    /**
     * @return bool
     */
    public function isBootstrapped()
    {
        try
        {
            return $this->redis->exists($this->key);
        }
        catch (\Throwable $e)
        {
            throw new StorageException('Failed to check for key existence', 0, $e);
        }
    }

    /**
     * @return void
     */
    public function remove()
    {
        try
        {
            if (!$this->redis->del($this->key))
            {
                throw new StorageException('Failed to delete key');
            }
        }
        catch (\Throwable $e)
        {
            throw new StorageException('Failed to delete key', 0, $e);
        }
    }

    /**
     * @SuppressWarnings(PHPMD)
     *
     * @param float $microtime
     *
     * @return void
     */
    public function setMicrotime($microtime)
    {
        try
        {
            $data = DoublePacker::pack($microtime);

            if (!$this->redis->set($this->key, $data))
            {
                throw new StorageException('Failed to store microtime');
            }
        }
        catch (\Throwable $e)
        {
            throw new StorageException('Failed to store microtime', 0, $e);
        }
    }

    /**
     * @SuppressWarnings(PHPMD)
     *
     * @return float
     */
    public function getMicrotime()
    {
        try
        {
            $data = $this->redis->get($this->key);
            if (false === $data)
            {
                throw new StorageException('Failed to get microtime');
            }

            return DoublePacker::unpack($data);
        }
        catch (\Throwable $e)
        {
            throw new StorageException('Failed to get microtime', 0, $e);
        }
    }

    /**
     * @return PHPRedisMutex
     */
    public function getMutex()
    {
        return $this->mutex;
    }

    /**
     * @return void
     */
    public function letMicrotimeUnchanged()
    {
    }
}
