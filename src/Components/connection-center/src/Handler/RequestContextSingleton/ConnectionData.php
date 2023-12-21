<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\RequestContextSingleton;

use Imi\ConnectionCenter\Contract\IConnection;

/**
 * 请求上下文单例连接数据.
 */
class ConnectionData
{
    /**
     * @var \WeakReference<IConnection>
     */
    protected \WeakReference $connection;

    public function __construct(IConnection $connection, protected string $contextFlag)
    {
        $this->setConnection($connection);
    }

    public function getContextFlag(): string
    {
        return $this->contextFlag;
    }

    public function setConnection(IConnection $connection): self
    {
        $this->connection = \WeakReference::create($connection);

        return $this;
    }

    /**
     * @return \WeakReference<IConnection>
     */
    public function getConnection(): \WeakReference
    {
        return $this->connection;
    }
}
