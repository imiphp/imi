<?php

declare(strict_types=1);

namespace Imi\ConnectionCenter\Handler\RequestContextSingleton;

use Imi\App;
use Imi\ConnectionCenter\Connection;
use Imi\ConnectionCenter\Contract\AbstractConnectionManager;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Contract\IConnectionManagerConfig;
use Imi\ConnectionCenter\Contract\IConnectionManagerStatistics;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\RequestContext;

/**
 * 请求上下文单例连接管理器.
 */
class RequestContextSingletonConnectionManager extends AbstractConnectionManager
{
    protected ?RequestContextSingletonConnectionManagerWritableStatistics $statistics = null;

    private static int $atomic = 0;

    private int $id = 0;

    /**
     * @var \WeakMap<object, ConnectionData>
     */
    private \WeakMap $instanceMap;

    public function __construct(IConnectionManagerConfig $config)
    {
        $this->config = $config;
        if ($config->isEnableStatistics())
        {
            $this->statistics = App::newInstance(RequestContextSingletonConnectionManagerWritableStatistics::class);
        }
        $this->id = ++self::$atomic;
        $this->instanceMap = new \WeakMap();
    }

    public static function createConfig(array $config): IConnectionManagerConfig
    {
        return new RequestContextSingletonConnectionManagerConfig(config: $config);
    }

    /**
     * 创建新连接.
     *
     * @return Connection
     */
    public function createConnection(): IConnection
    {
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }
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
        if (!$this->available)
        {
            throw new \RuntimeException('Connection manager is unavailable');
        }

        return RequestContext::use(function (\ArrayObject $context) {
            if ($enableStatistics = $this->config->isEnableStatistics())
            {
                $beginTime = microtime(true);
            }

            if (isset($context[static::class][$this->id]))
            {
                // 从连接上下文拿连接实例
                $instance = $context[static::class][$this->id];
                if (!isset($this->instanceMap[$instance]))
                {
                    throw new \RuntimeException('Connection is not in this connection manager');
                }
                $connectionData = $this->instanceMap[$instance];
                $connection = $connectionData->getConnection()->get();
                if (!$connection || ConnectionStatus::Available !== $connection->getStatus())
                {
                    $connection = new Connection($this, $instance);
                    $connectionData->setConnection($connection);
                }
            }
            else
            {
                // 创建新实例
                $connection = $this->createConnection();
                $instance = $connection->getInstance();
                $this->instanceMap[$instance] = new ConnectionData($connection, RequestContext::getCurrentFlag());
                $context[static::class][$this->id] = $instance;
                if ($enableStatistics)
                {
                    $this->statistics->changeTotalConnectionCount(1);
                    $this->statistics->changeUsedConnectionCount(1);
                }
            }

            if ($enableStatistics)
            {
                $this->statistics->setGetConnectionTime(microtime(true) - $beginTime);
                $this->statistics->addGetConnectionTimes();
            }

            return $connection;
        });
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
        if (isset($this->instanceMap[$instance]))
        {
            $connectionData = $this->instanceMap[$instance];
            $context = RequestContext::getInstance()->get($connectionData->getContextFlag());
            if (isset($context[static::class][$this->id]) && $context[static::class][$this->id] === $instance)
            {
                // 删除连接上下文实例
                unset($context[static::class][$this->id]);
            }
            if ($enableStatistics = $this->config->isEnableStatistics())
            {
                $this->statistics->changeTotalConnectionCount(-1);
                $this->statistics->changeUsedConnectionCount(-1);
            }
        }
        // 关闭连接
        $this->getDriver()->close($instance);

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
        $instance = $connection->getInstance();
        if (!isset($this->instanceMap[$instance]))
        {
            throw new \RuntimeException('Connection is not in this connection manager');
        }
        $connectionData = $this->instanceMap[$instance];
        $context = RequestContext::getInstance()->get($connectionData->getContextFlag());
        if (isset($context[static::class][$this->id]) && $context[static::class][$this->id] === $instance)
        {
            // 删除连接上下文实例
            unset($context[static::class][$this->id]);
        }
        // 移除关联
        unset($this->instanceMap[$instance]);
    }

    protected function __close(): void
    {
        foreach ($this->instanceMap as $instance => $connectionData)
        {
            $connection = $connectionData->getConnection()->get();
            if ($connection)
            {
                $connection->release();
            }
            else
            {
                // 正常情况下不可到达此分支
                $this->getDriver()->close($instance);
            }
        }
    }

    /**
     * @return RequestContextSingletonConnectionManagerStatistics
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
