<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\Singleton;

use Imi\App;
use Imi\ConnectionCenter\Connection;
use Imi\ConnectionCenter\Contract\AbstractConnectionManager;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Contract\IConnectionManagerConfig;
use Imi\ConnectionCenter\Contract\IConnectionManagerStatistics;
use Imi\ConnectionCenter\Enum\ConnectionStatus;

/**
 * 单例连接管理器.
 */
class SingletonConnectionManager extends AbstractConnectionManager
{
    protected ?SingletonConnectionManagerWritableStatistics $statistics = null;

    private ?IConnection $connection = null;

    public function __construct(IConnectionManagerConfig $config)
    {
        $this->config = $config;
        if ($config->isEnableStatistics())
        {
            $this->statistics = App::newInstance(SingletonConnectionManagerWritableStatistics::class);
        }
    }

    public static function createConfig(array $config): IConnectionManagerConfig
    {
        return new SingletonConnectionManagerConfig(config: $config);
    }

    /**
     * 创建新连接.
     *
     * @return Connection
     */
    public function createConnection(): IConnection
    {
        $instance = $this->createInstance();
        if ($this->config->isEnableStatistics())
        {
            $this->statistics->addCreateConnectionTimes();
        }

        return new Connection($this, $instance);
    }

    public function getConnection(): IConnection
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
        if ($enableStatistics = $this->config->isEnableStatistics())
        {
            $beginTime = microtime(true);
        }
        if (!$this->connection || ConnectionStatus::Available !== $this->connection->getStatus())
        {
            // 创建新实例
            $this->connection = $this->createConnection();
            if ($enableStatistics)
            {
                $this->statistics->changeTotalConnectionCount(1);
                $this->statistics->changeUsedConnectionCount(1);
            }
        }
        else
        {
            $driver = $this->getDriver();
            if ($this->config->isCheckStateWhenGetResource() && !$driver->checkAvailable($instance = $this->connection->getInstance()))
            {
                try
                {
                    $driver->close($instance);
                    $instance = $driver->connect($instance);
                    $this->connection = new Connection($this, $instance);
                }
                catch (\Throwable $th)
                {
                    $this->connection = null;
                    throw $th;
                }
            }
        }
        if ($enableStatistics)
        {
            $this->statistics->setGetConnectionTime(microtime(true) - $beginTime);
            $this->statistics->addGetConnectionTimes();
        }

        return $this->connection;
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
        $instance = $connection->getInstance();
        // 关闭连接
        $this->getDriver()->close($instance);
        if ($this->connection === $connection)
        {
            $this->connection = null;
            if ($enableStatistics = $this->config->isEnableStatistics())
            {
                $this->statistics->changeTotalConnectionCount(-1);
                $this->statistics->changeUsedConnectionCount(-1);
            }
        }
        if ($enableStatistics ?? $this->config->isEnableStatistics())
        {
            $this->statistics->addReleaseConnectionTimes();
        }
    }

    public function detachConnection(IConnection $connection): void
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
        if ($connection->getManager() !== $this)
        {
            throw new \RuntimeException(sprintf('Connection manager %s cannot release connection, because the connection manager of this connection is %s', static::class, $connection->getManager()::class));
        }
        if ($this->connection !== $connection)
        {
            throw new \RuntimeException('Connection is not in this connection manager');
        }
        $this->connection = null;
    }

    protected function __close(): void
    {
        if ($this->connection)
        {
            $this->connection->release();
            $this->connection = null;
        }
    }

    /**
     * @return SingletonConnectionManagerStatistics
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
