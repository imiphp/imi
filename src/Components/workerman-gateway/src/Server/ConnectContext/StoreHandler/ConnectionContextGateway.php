<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Server\ConnectionContext\StoreHandler;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\Server\ConnectionContext\StoreHandler\IHandler;
use Imi\Timer\Timer;

/**
 * Workerman Gateway 连接上下文处理器.
 *
 * @Bean("ConnectionContextGateway")
 */
class ConnectionContextGateway implements IHandler
{
    /**
     * 读取数据.
     */
    public function read(string $key): array
    {
        if (!$this->checkKey($key))
        {
            return [];
        }

        return Gateway::getSession($key) ?: [];
    }

    /**
     * 保存数据.
     */
    public function save(string $key, array $data): void
    {
        if (!$this->checkKey($key))
        {
            return;
        }
        Gateway::setSession($key, $data);
    }

    /**
     * 销毁数据.
     */
    public function destroy(string $key): void
    {
        if (!$this->checkKey($key))
        {
            return;
        }
        Gateway::setSession($key, []);
    }

    /**
     * 延迟销毁数据.
     */
    public function delayDestroy(string $key, int $ttl): void
    {
        if (!$this->checkKey($key))
        {
            return;
        }
        Timer::after($ttl * 1000, function () use ($key) {
            $this->destroy($key);
        });
    }

    /**
     * 数据是否存在.
     */
    public function exists(string $key): bool
    {
        if (!$this->checkKey($key))
        {
            return false;
        }

        return (bool) $this->read($key);
    }

    /**
     * 加锁
     */
    public function lock(string $key, ?callable $callable = null): bool
    {
        if (!$this->checkKey($key))
        {
            return false;
        }
        if ($callable)
        {
            $callable();
        }

        return true;
    }

    /**
     * 解锁
     */
    public function unlock(): bool
    {
        return true;
    }

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

    private function checkKey(string $key): bool
    {
        return 20 === \strlen($key);
    }
}
