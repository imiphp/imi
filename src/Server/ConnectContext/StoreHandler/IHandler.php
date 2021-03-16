<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\StoreHandler;

/**
 * 连接上下文存储处理器.
 */
interface IHandler
{
    /**
     * 读取数据.
     */
    public function read(string $key): array;

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void;

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void;

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void;

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool;

    /**
     * 加锁
     */
    public function lock(string $key, ?callable $callable = null): bool;

    /**
     * 解锁
     */
    public function unlock(): bool;
}
