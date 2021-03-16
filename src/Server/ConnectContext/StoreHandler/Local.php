<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Lock;
use Imi\Timer\Timer;

/**
 * 连接上下文存储处理器-Local.
 *
 * @Bean("ConnectContextLocal")
 */
class Local implements IHandler
{
    /**
     * 存储集合.
     */
    private array $storeMap = [];

    /**
     * 锁 ID.
     */
    protected ?string $lockId = null;

    /**
     * 读取数据.
     */
    public function read(string $key): array
    {
        return $this->storeMap[$key] ?? [];
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        $this->storeMap[$key] = $data;
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        $storeMap = &$this->storeMap;
        if (isset($storeMap[$key]))
        {
            unset($storeMap[$key]);
        }
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        Timer::after($ttl * 1000, function () use ($key) {
            $this->destroy($key);
        });
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        return isset($this->storeMap[$key]);
    }

    /**
     * 加锁
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        if (null === $this->lockId)
        {
            $callable();

            return true;
        }
        else
        {
            return Lock::getInstance($this->lockId, $key)->lock($callable);
        }
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        if (null === $this->lockId)
        {
            return true;
        }
        else
        {
            return Lock::unlock($this->lockId);
        }
    }
}
