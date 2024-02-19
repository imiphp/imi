<?php

declare(strict_types=1);

namespace Imi\Redis;

use Imi\Config;
use Imi\ConnectionCenter\Contract\IConnection;
use Imi\ConnectionCenter\Enum\ConnectionStatus;
use Imi\ConnectionCenter\Facade\ConnectionCenter;
use Imi\Redis\Handler\IRedisHandler;
use Imi\Redis\Handler\PhpRedisClusterHandler;
use Imi\Redis\Handler\PhpRedisHandler;
use Imi\Redis\Handler\PredisClusterHandler;
use Imi\Redis\Handler\PredisHandler;

/**
 * @template InstanceLink of object{count: int, connection: IConnection}
 */
class RedisManager
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * @var \WeakMap<IRedisHandler, InstanceLink>
     */
    private static \WeakMap $instanceLinkConnectionMap;

    /**
     * 获取新的 Redis 连接实例.
     *
     * @param string|null $poolName 连接池名称
     * @return PhpRedisHandler|PhpRedisClusterHandler|PredisHandler|PredisClusterHandler
     */
    public static function getNewInstance(?string $poolName = null): IRedisHandler
    {
        $poolName = self::parsePoolName($poolName);
        $manager = ConnectionCenter::getConnectionManager($poolName);
        /** @var IRedisHandler $instance */
        $instance = $manager->getDriver()->createInstance();

        return $instance;
    }

    /**
     * 获取 Redis 连接实例，每个RequestContext中共用一个.
     *
     * @param string $poolName 连接池名称
     * @return PhpRedisHandler|PhpRedisClusterHandler|PredisHandler|PredisClusterHandler
     */
    public static function getInstance(?string $poolName = null): IRedisHandler
    {
        $poolName = self::parsePoolName($poolName);
        $connection = ConnectionCenter::getRequestContextConnection($poolName);
        /** @var IRedisHandler $instance */
        $instance = $connection->getInstance();

        return $instance;
    }

    /**
     * 使用回调来使用池子中的资源，无需手动释放
     * 回调有两个参数：$connection(连接对象), $instance(操作实例对象，Redis实例)
     * 本方法返回值为回调的返回值
     */
    public static function use(?string $poolName, callable $callable): mixed
    {
        $poolName = self::parsePoolName($poolName);

        if (ConnectionCenter::hasConnectionManager($poolName))
        {
            $connection = ConnectionCenter::getConnection($poolName);

            return $callable($connection->getInstance());
        }
        else
        {
            return $callable(static::getInstance($poolName));
        }
    }

    protected static function recordInstanceLinkPool(IRedisHandler $handler, IConnection $connection): void
    {
        if (!isset(self::$instanceLinkConnectionMap))
        {
            self::$instanceLinkConnectionMap = new \WeakMap();
        }

        $ref = self::$instanceLinkConnectionMap[$handler] ?? new \stdClass();
        $ref->connection = $connection;
        $ref->count = ($ref->count ?? 0) + 1;

        self::$instanceLinkConnectionMap[$handler] = $ref;
    }

    protected static function getConnectionByInstance(IRedisHandler $handler): ?IConnection
    {
        return (self::$instanceLinkConnectionMap[$handler] ?? null)?->connection;
    }

    protected static function unsetConnectionInstance(IRedisHandler $handler): bool
    {
        if (!isset(self::$instanceLinkConnectionMap[$handler]))
        {
            return true;
        }
        /** @var InstanceLink $ref */
        $ref = self::$instanceLinkConnectionMap[$handler];
        if ($ref->count > 1)
        {
            --$ref->count;

            return false;
        }
        else
        {
            unset(self::$instanceLinkConnectionMap[$handler]);

            return true;
        }
    }

    /**
     * 释放 Redis 连接实例.
     * @deprecated
     */
    public static function release(IRedisHandler $redis): void
    {
        $connection = self::getConnectionByInstance($redis);
        if (null === $connection)
        {
            throw new \RuntimeException('RedisHandler is not a valid connection center connection instance');
        }
        if (self::unsetConnectionInstance($redis))
        {
            ConnectionStatus::Available === $connection->getStatus() && $connection->release();
        }
    }

    /**
     * 处理连接池 名称.
     */
    public static function parsePoolName(?string $poolName = null): string
    {
        if (null === $poolName || '' === $poolName)
        {
            $poolName = static::getDefaultPoolName();
        }

        return $poolName;
    }

    /**
     * 获取默认池子名称.
     */
    public static function getDefaultPoolName(): string
    {
        return Config::get('@currentServer.redis.defaultPool');
    }

    /**
     * 从当前上下文中获取公用连接.
     * @deprecated
     */
    public static function isQuickFromRequestContext(): bool
    {
        return Config::get('@currentServer.redis.quickFromRequestContext', true);
    }
}
