<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\ConnectContext\BinderHandler;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\Server\ConnectContext\BinderHandler\IHandler;

/**
 * Workerman Gateway 连接绑定器.
 *
 * @Bean("ConnectionBinderGateway")
 */
class ConnectionBinderGateway implements IHandler
{
    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string $clientId
     */
    public function bind(string $flag, $clientId): void
    {
        Gateway::bindUid($clientId, $flag);
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string $clientId
     */
    public function bindNx(string $flag, $clientId): bool
    {
        Gateway::bindUid($clientId, $flag);

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
        Gateway::unbindUid($clientId, $flag);
    }

    /**
     * 使用标记获取连接编号.
     */
    public function getClientIdByFlag(string $flag): array
    {
        return Gateway::getClientIdByUid($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     */
    public function getClientIdsByFlags(array $flags): array
    {
        $result = [];
        foreach ($flags as $flag)
        {
            $result[$flag] = Gateway::getClientIdByUid($flag);
        }

        return $result;
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public function getFlagByClientId($clientId): ?string
    {
        return Gateway::getUidByClientId($clientId);
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
        $flags = [];
        foreach ($clientIds as $clientId)
        {
            $flags[$clientId] = Gateway::getUidByClientId($clientId);
        }

        return $flags;
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldClientIdByFlag(string $flag): ?int
    {
        return null;
    }
}
