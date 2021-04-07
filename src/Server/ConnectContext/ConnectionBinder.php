<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\ConnectContext\BinderHandler\IHandler;

/**
 * 连接绑定器.
 *
 * @Bean("ConnectionBinder")
 */
class ConnectionBinder
{
    /**
     * 处理器类.
     */
    protected string $handlerClass = 'ConnectionBinderRedis';

    /**
     * 处理器对象.
     */
    private IHandler $handler;

    public function __init(): void
    {
        $this->handler = App::getBean($this->handlerClass);
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
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     */
    public function unbind(string $flag, int $keepTime = null): void
    {
        $this->handler->unbind($flag, $keepTime);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @return int|string|null
     */
    public function getClientIdByFlag(string $flag)
    {
        return $this->handler->getClientIdByFlag($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]|string[]
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
}
