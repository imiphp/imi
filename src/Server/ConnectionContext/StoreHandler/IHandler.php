<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext\StoreHandler;

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

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void;

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string $clientId
     */
    public function bindNx(string $flag, $clientId): bool;

    /**
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void;

    /**
     * 使用标记获取连接编号.
     */
    public function getClientIdByFlag(string $flag): array;

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     */
    public function getClientIdsByFlags(array $flags): array;

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public function getFlagByClientId($clientId): ?string;

    /**
     * 使用连接编号获取标记.
     *
     * @param int[]|string[] $clientIds
     *
     * @return string[]
     */
    public function getFlagsByClientIds(array $clientIds): array;

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldClientIdByFlag(string $flag): ?int;
}
