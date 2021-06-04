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
    private IHandler $handler;

    public function __init(): void
    {
        $this->handler = RequestContext::getServerBean($this->handlerClass);
    }

    /**
     * 读取数据.
     */
    public function read(string $key): array
    {
        return $this->handler->read($key);
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        $this->handler->save($key, $data);
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        $this->handler->destroy($key);
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        $this->handler->delayDestroy($key, $ttl);
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        return $this->handler->exists($key);
    }

    /**
     * 加锁
     *
     * @param callable $callable
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        return $this->handler->lock($key, $callable);
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        return $this->handler->unlock();
    }

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void
    {
        $this->handler->bind($flag, $clientId);
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string $clientId
     */
    public function bindNx(string $flag, $clientId): bool
    {
        return $this->handler->bindNx($flag, $clientId);
    }

    /**
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        $this->handler->unbind($flag, $clientId, $keepTime);
    }

    /**
     * 使用标记获取连接编号.
     */
    public function getClientIdByFlag(string $flag): array
    {
        return $this->handler->getClientIdByFlag($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     */
    public function getClientIdsByFlags(array $flags): array
    {
        return $this->handler->getClientIdsByFlags($flags);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public function getFlagByClientId($clientId): ?string
    {
        return $this->handler->getFlagByClientId($clientId);
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
        return $this->handler->getFlagsByClientIds($clientIds);
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldClientIdByFlag(string $flag): ?int
    {
        return $this->handler->getOldClientIdByFlag($flag);
    }

    /**
     * Get 设为 0 则连接断开立即销毁数据.
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }
}
