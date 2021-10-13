<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\StoreHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Lock;
use Imi\Timer\Timer;

/**
 * 连接上下文存储处理器-Local.
 *
 * @Bean("ConnectionContextLocal")
 */
class Local implements IHandler
{
    /**
     * 锁 ID.
     */
    protected ?string $lockId = null;

    /**
     * 清除旧的过期数据时间间隔，单位：秒.
     */
    protected float $gcInteval = 60;

    /**
     * 存储集合.
     */
    private array $storeMap = [];

    /**
     * 标记数据.
     */
    private array $flagsMap = [];

    /**
     * 连接号数据.
     */
    private array $clientIdsMap = [];

    /**
     * 旧数据.
     */
    private array $oldDataMap = [];

    public function __init(): void
    {
        if ($this->gcInteval > 0)
        {
            Timer::tick((int) ($this->gcInteval * 1000), [$this, 'gc']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $key): array
    {
        return $this->storeMap[$key] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $key, array $data): void
    {
        $this->storeMap[$key] = $data;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        Timer::after($ttl * 1000, function () use ($key) {
            $this->destroy($key);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $key): bool
    {
        return isset($this->storeMap[$key]);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     */
    public function bind(string $flag, $clientId): void
    {
        $this->flagsMap[$clientId] = $flag;
        $this->clientIdsMap[$flag][] = $clientId;
    }

    /**
     * {@inheritDoc}
     */
    public function bindNx(string $flag, $clientId): bool
    {
        if (isset($this->flagsMap[$clientId]) || isset($this->clientIdsMap[$flag]))
        {
            return false;
        }
        $this->bind($flag, $clientId);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        if (null !== $keepTime)
        {
            $this->oldDataMap[$flag] = [
                'flag'           => $flag,
                'clientId'       => $clientId,
                'keepTime'       => time() + $keepTime,
            ];
        }
        if (isset($this->flagsMap[$clientId]))
        {
            unset($this->flagsMap[$clientId]);
        }
        if (isset($this->clientIdsMap[$flag]))
        {
            $index = array_search($clientId, $this->clientIdsMap[$flag]);
            if (false !== $index)
            {
                unset($this->clientIdsMap[$flag][$index]);
            }
            if (!$this->clientIdsMap[$flag])
            {
                unset($this->clientIdsMap[$flag]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIdByFlag(string $flag): array
    {
        return (array) ($this->clientIdsMap[$flag] ?? []);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIdsByFlags(array $flags): array
    {
        $clientIdsMap = &$this->clientIdsMap;
        $clientIds = [];
        foreach ($flags as $flag)
        {
            if (isset($clientIdsMap[$flag]))
            {
                $clientIds[$flag] = $clientIdsMap[$flag];
            }
        }

        return $clientIds;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagByClientId($clientId): ?string
    {
        return $this->flagsMap[$clientId] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagsByClientIds(array $clientIds): array
    {
        $flagsMap = &$this->flagsMap;
        $flags = [];
        foreach ($clientIds as $clientId)
        {
            if (isset($flagsMap[$clientId]))
            {
                $flags[$clientId] = $flagsMap[$clientId];
            }
        }

        return $flags;
    }

    /**
     * {@inheritDoc}
     */
    public function getOldClientIdByFlag(string $flag): ?int
    {
        $oldDataMap = &$this->oldDataMap;
        $oldData = $oldDataMap[$flag] ?? null;
        if (!$oldData)
        {
            return null;
        }
        // 过期处理
        if ($oldData['keepTime'] < time())
        {
            unset($oldDataMap[$flag]);

            return null;
        }

        return $oldData['clientId'];
    }

    /**
     * {@inheritDoc}
     */
    public function gc(): void
    {
        $oldDataMap = &$this->oldDataMap;
        if ($oldDataMap)
        {
            $time = time();
            foreach ($oldDataMap as $flag => $data)
            {
                if ($data['keepTime'] < $time)
                {
                    unset($oldDataMap[$flag]);
                }
            }
        }
    }
}
