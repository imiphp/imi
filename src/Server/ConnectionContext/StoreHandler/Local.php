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

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void
    {
        $this->flagsMap[$clientId] = $flag;
        $this->clientIdsMap[$flag][] = $clientId;
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string $clientId
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
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
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
        }
    }

    /**
     * 使用标记获取连接编号.
     */
    public function getClientIdByFlag(string $flag): array
    {
        return (array) $this->clientIdsMap[$flag] ?? [];
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
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
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public function getFlagByClientId($clientId): ?string
    {
        return $this->flagsMap[$clientId] ?? null;
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[]|string[] $clientIds
     *
     * @return string[]
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
     * 使用标记获取旧的连接编号.
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
     * 清除旧的过期数据.
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
