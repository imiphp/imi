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

    /**
     * Redis 连接池名称，仅兼容 ConnectionBinderRedis 时有效.
     *
     * @deprecated 2.0
     */
    protected ?string $redisPool = null;

    /**
     * redis中第几个库，仅兼容 ConnectionBinderRedis 时有效.
     *
     * @deprecated 2.0
     */
    protected ?int $redisDb = null;

    /**
     * 键，仅兼容 ConnectionBinderRedis 时有效.
     *
     * @deprecated 2.0
     */
    protected ?string $key = null;

    public function __init(): void
    {
        $this->handler = App::getBean($this->handlerClass);
    }

    /**
     * 绑定一个标记到当前连接.
     */
    public function bind(string $flag, int $fd): void
    {
        $this->handler->bind($flag, $fd);
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     */
    public function bindNx(string $flag, int $fd): bool
    {
        return $this->handler->bindNx($flag, $fd);
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
     */
    public function getFdByFlag(string $flag): ?int
    {
        return $this->handler->getFdByFlag($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     *
     * @return int[]
     */
    public function getFdsByFlags(array $flags): array
    {
        return $this->handler->getFdsByFlags($flags);
    }

    /**
     * 使用连接编号获取标记.
     */
    public function getFlagByFd(int $fd): ?string
    {
        return $this->handler->getFlagByFd($fd);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[] $fds
     *
     * @return string[]
     */
    public function getFlagsByFds(array $fds): array
    {
        return $this->handler->getFlagsByFds($fds);
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public function getOldFdByFlag(string $flag): ?int
    {
        return $this->handler->getOldFdByFlag($flag);
    }
}
