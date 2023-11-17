<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\AlwaysNew;

use Imi\App;
use Imi\ConnectionCenter\Connection;
use Imi\ConnectionCenter\Contract\AbstractConnectionManager;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Contract\IConnectionManagerConfig;
use Imi\ConnectionCenter\Contract\IConnectionManagerStatistics;
use Imi\ConnectionCenter\Enum\ConnectionStatus;

/**
 * 总是创建新连接管理器.
 */
class AlwaysNewConnectionManager extends AbstractConnectionManager
{
    protected ?AlwaysNewConnectionManagerWritableStatistics $statistics = null;

    public function __construct(IConnectionManagerConfig $config)
    {
        $this->config = $config;
        if ($config->isEnableStatistics())
        {
            $this->statistics = App::newInstance(AlwaysNewConnectionManagerWritableStatistics::class);
        }
    }

    public static function createConfig(array $config): IConnectionManagerConfig
    {
        return new AlwaysNewConnectionManagerConfig(config: $config);
    }

    /**
     * 创建新连接.
     *
     * @return Connection
     */
    public function createConnection(): IConnection
    {
        $driver = $this->getDriver();
        // 创建连接
        $instance = $driver->createInstance();
        // 连接
        $driver->connect($instance);
        if ($this->config->isEnableStatistics())
        {
            $this->statistics->addCreateConnectionTimes();
        }

        return new Connection($this, $instance);
    }

    /**
     * @return Connection
     */
    public function getConnection(): IConnection
    {
        if ($enableStatistics = $this->config->isEnableStatistics())
        {
            $beginTime = microtime(true);
        }
        $result = $this->createConnection();
        if ($enableStatistics)
        {
            $this->statistics->setGetConnectionTime(microtime(true) - $beginTime);
            $this->statistics->addGetConnectionTimes();
        }

        return $result;
    }

    public function releaseConnection(IConnection $connection): void
    {
        if ($connection->getManager() !== $this)
        {
            throw new \RuntimeException(sprintf('Connection manager %s cannot release connection, because the connection manager of this connection is %s', static::class, $connection->getManager()::class));
        }
        if (ConnectionStatus::WaitRelease !== $connection->getStatus())
        {
            throw new \RuntimeException('Connection is not in wait release status');
        }
        $this->getDriver()->close($connection->getInstance());
        if ($this->config->isEnableStatistics())
        {
            $this->statistics->addReleaseConnectionTimes();
        }
    }

    public function detachConnection(IConnection $connection): void
    {
        // 本管理器创建的连接本来就是分离的，无需处理
    }

    protected function __close(): void
    {
        // 本管理器创建的连接本来就是分离的，无需处理
    }

    /**
     * @return AlwaysNewConnectionManagerStatistics
     */
    public function getStatistics(): IConnectionManagerStatistics
    {
        if ($this->config->isEnableStatistics())
        {
            return $this->statistics->toStatus();
        }
        else
        {
            throw new \RuntimeException('Connection manager statistics is disabled');
        }
    }
}
