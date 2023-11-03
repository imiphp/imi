<?php

declare(strict_types=1);

namespace Imi\RateLimit\Storage;

use bandwidthThrottle\tokenBucket\storage\scope\GlobalScope;
use bandwidthThrottle\tokenBucket\storage\Storage;
use bandwidthThrottle\tokenBucket\storage\StorageException;
use bandwidthThrottle\tokenBucket\util\DoublePacker;
use Imi\Redis\RedisHandler;
use malkusch\lock\mutex\PHPRedisMutex;

/**
 * Imi Redis 存储器.
 */
final class ImiRedisStorage implements Storage, GlobalScope
{
    /**
     * The mutex.
     */
    private readonly PHPRedisMutex $mutex;

    /**
     * Sets the connected Redis API.
     *
     * The Redis API needs to be connected yet. I.e. Redis::connect() was
     * called already.
     *
     * @param string                  $key   the resource name
     * @param \Imi\Redis\RedisHandler $redis the Redis API
     */
    public function __construct(private readonly string $key, private readonly RedisHandler $redis, int $timeout = 3)
    {
        // @phpstan-ignore-next-line
        $this->mutex = new PHPRedisMutex([$redis], $key, $timeout);
    }

    /**
     * Bootstraps the storage.
     *
     * @param float $microtime the timestamp
     *
     * @throws StorageException bootstrapping failed
     *
     * @internal
     */
    public function bootstrap(mixed $microtime): void
    {
        $this->setMicrotime($microtime);
    }

    public function isBootstrapped(): bool
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

    public function remove(): void
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
     * Stores a timestamp.
     *
     * @param float $microtime the timestamp
     *
     * @throws StorageException writing to the storage failed
     *
     * @internal
     */
    public function setMicrotime(mixed $microtime): void
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
     */
    public function getMicrotime(): float
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

    public function getMutex(): PHPRedisMutex
    {
        return $this->mutex;
    }

    public function letMicrotimeUnchanged(): void
    {
    }
}
