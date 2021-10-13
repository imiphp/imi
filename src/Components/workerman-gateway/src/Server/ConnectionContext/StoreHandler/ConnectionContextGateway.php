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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function unlock(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bind(string $flag, $clientId): void
    {
        Gateway::bindUid($clientId, $flag);
    }

    /**
     * {@inheritDoc}
     */
    public function bindNx(string $flag, $clientId): bool
    {
        Gateway::bindUid($clientId, $flag);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        Gateway::unbindUid($clientId, $flag);
    }

    /**
     * {@inheritDoc}
     */
    public function getClientIdByFlag(string $flag): array
    {
        return Gateway::getClientIdByUid($flag);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getFlagByClientId($clientId): ?string
    {
        return Gateway::getUidByClientId($clientId);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
