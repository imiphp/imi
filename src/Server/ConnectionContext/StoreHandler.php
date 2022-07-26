<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\ConnectionContext\StoreHandler\IHandler;

/**
 * 连接上下文存储处理器-总.
 *
 * @Bean("ConnectionContextStore")
 */
class StoreHandler implements IHandler
{
    /**
     * 处理器类.
     */
    protected string $handlerClass = \Imi\Server\ConnectionContext\StoreHandler\Local::class;

    /**
     * 数据有效期，单位：秒
     * 连接断开后，供断线重连的，数据保留时间
     * 设为 0 则连接断开立即销毁数据.
     */
    protected int $ttl = 0;

    /**
     * 处理器对象
     */
    private ?IHandler $handler = null;

    /**
     * 获取处理器对象
     */
    public function getHandler(): IHandler
    {
        return $this->handler ??= RequestContext::getServerBean($this->handlerClass);
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $key): array
    {
        return $this->getHandler()->read($key);
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $key, array $data): void
    {
        $this->getHandler()->save($key, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $key): void
    {
        $this->getHandler()->destroy($key);
    }

    /**
     * {@inheritDoc}
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        $this->getHandler()->delayDestroy($key, $ttl);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $key): bool
    {
        return $this->getHandler()->exists($key);
    }

    /**
     * {@inheritDoc}
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        return $this->getHandler()->lock($key, $callable);
    }

    /**
     * {@inheritDoc}
     */
    public function unlock(): bool
    {
        return $this->getHandler()->unlock();
    }

    /**
     * {@inheritDoc}
     */
    public function bind(string $flag, $clientId): void
    {
        $this->getHandler()->bind($flag, $clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function bindNx(string $flag, $clientId): bool
    {
        return $this->getHandler()->bindNx($flag, $clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        $this->getHandler()->unbind($flag, $clientId, $keepTime);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIdByFlag(string $flag): array
    {
        return $this->getHandler()->getClientIdByFlag($flag);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIdsByFlags(array $flags): array
    {
        return $this->getHandler()->getClientIdsByFlags($flags);
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagByClientId($clientId): ?string
    {
        return $this->getHandler()->getFlagByClientId($clientId);
    }

    /**
     * {@inheritDoc}
     */
    public function getFlagsByClientIds(array $clientIds): array
    {
        return $this->getHandler()->getFlagsByClientIds($clientIds);
    }

    /**
     * {@inheritDoc}
     */
    public function getOldClientIdByFlag(string $flag): ?int
    {
        return $this->getHandler()->getOldClientIdByFlag($flag);
    }

    /**
     * Get 设为 0 则连接断开立即销毁数据.
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }
}
