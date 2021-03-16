<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ConnectContext\StoreHandler\IHandler;

/**
 * 连接上下文存储处理器-总.
 *
 * @Bean("ConnectContextStore")
 */
class StoreHandler implements IHandler
{
    /**
     * 处理器类.
     */
    protected string $handlerClass = \Imi\Server\ConnectContext\StoreHandler\Redis::class;

    /**
     * 数据有效期，单位：秒
     * 连接断开后，供断线重连的，数据保留时间
     * 设为 0 则连接断开立即销毁数据.
     */
    protected int $ttl = 0;

    /**
     * 读取数据.
     */
    public function read(string $key): array
    {
        return $this->getHandler()->read($key);
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        $this->getHandler()->save($key, $data);
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        $this->getHandler()->destroy($key);
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        $this->getHandler()->delayDestroy($key, $ttl);
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        return $this->getHandler()->exists($key);
    }

    /**
     * 加锁
     *
     * @param callable $callable
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        return $this->getHandler()->lock($key, $callable);
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        return $this->getHandler()->unlock();
    }

    /**
     * 获取处理器.
     */
    public function getHandler(): IHandler
    {
        return RequestContext::getServerBean($this->handlerClass);
    }

    /**
     * Get 设为 0 则连接断开立即销毁数据.
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }
}
