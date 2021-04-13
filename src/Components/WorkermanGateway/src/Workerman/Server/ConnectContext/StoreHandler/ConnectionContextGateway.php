<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\ConnectContext\StoreHandler;

use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\Server\ConnectContext\StoreHandler\IHandler;
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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

    private function checkKey(string $key): bool
    {
        return 20 === \strlen($key);
    }
}
