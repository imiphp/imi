<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\BinderHandler;

/**
 * 连接绑定器存储器.
 */
interface IHandler
{
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
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, ?int $keepTime = null): void;

    /**
     * 使用标记获取连接编号.
     *
     * @return int|string|null
     */
    public function getClientIdByFlag(string $flag);

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]|string[]
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
