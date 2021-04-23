<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\BinderHandler;

use Imi\Bean\Annotation\Bean;
use Imi\Timer\Timer;

/**
 * 连接绑定器本地驱动.
 *
 * @Bean("ConnectionBinderLocal")
 */
class Local implements IHandler
{
    /**
     * 清除旧的过期数据时间间隔，单位：秒.
     */
    protected float $gcInteval = 60;

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
