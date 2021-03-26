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
    private array $fdsMap = [];

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
     */
    public function bind(string $flag, int $fd): void
    {
        $this->flagsMap[$fd] = $flag;
        $this->fdsMap[$flag] = $fd;
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     */
    public function bindNx(string $flag, int $fd): bool
    {
        if (isset($this->flagsMap[$fd]) || isset($this->fdsMap[$flag]))
        {
            return false;
        }
        $this->bind($flag, $fd);

        return true;
    }

    /**
     * 取消绑定.
     *
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, ?int $keepTime = null): void
    {
        $fd = $this->getFdByFlag($flag);
        if (null === $fd)
        {
            return;
        }
        if (null !== $keepTime)
        {
            $this->oldDataMap[$flag] = [
                'flag'     => $flag,
                'fd'       => $fd,
                'keepTime' => time() + $keepTime,
            ];
        }
        if (isset($this->flagsMap[$fd]))
        {
            unset($this->flagsMap[$fd]);
        }
        if (isset($this->fdsMap[$flag]))
        {
            unset($this->fdsMap[$flag]);
        }
    }

    /**
     * 使用标记获取连接编号.
     */
    public function getFdByFlag(string $flag): ?int
    {
        return $this->fdsMap[$flag] ?? null;
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]
     */
    public function getFdsByFlags(array $flags): array
    {
        $fdsMap = &$this->fdsMap;
        $fds = [];
        foreach ($flags as $flag)
        {
            if (isset($fdsMap[$flag]))
            {
                $fds[] = $fdsMap[$flag];
            }
        }

        return $fds;
    }

    /**
     * 使用连接编号获取标记.
     */
    public function getFlagByFd(int $fd): ?string
    {
        return $this->flagsMap[$fd] ?? null;
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[] $fds
     *
     * @return string[]
     */
    public function getFlagsByFds(array $fds): array
    {
        $flagsMap = &$this->flagsMap;
        $flags = [];
        foreach ($fds as $fd)
        {
            if (isset($flagsMap[$fd]))
            {
                $flags[] = $flagsMap[$fd];
            }
        }

        return $flags;
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldFdByFlag(string $flag): ?int
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

        return $oldData['fd'];
    }

    /**
     * 清除旧的过期数据.
     */
    public function gc(): void
    {
        $oldDataMap = &$this->oldDataMap;
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
