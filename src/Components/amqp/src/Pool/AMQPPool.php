<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\App;
use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * AMQP 客户端连接池.
 */
class AMQPPool
{
    /**
     * 连接配置.
     */
    private static ?array $connections = null;

    private function __construct()
    {
    }

    /**
     * 获取新的连接实例.
     *
     * @param string $poolName 连接池名称
     *
     * @return \PhpAmqpLib\Connection\AbstractConnection
     */
    public static function getNewInstance($poolName = null)
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getResource($poolName)->getInstance();
        }
        else
        {
            $config = Config::get('@app.amqp.connections.' . $poolName);
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
            }

            /** @var AbstractConnection $connection */
            $connection = App::getBean($config['connectionClass'] ?? AMQPStreamConnection::class, $config['host'], $config['port'], $config['user'], $config['password'], $config['vhost'] ?? '/', $config['insist'] ?? false, $config['loginMethod'] ?? 'AMQPLAIN', $config['loginResponse'] ?? null, $config['locale'] ?? 'en_US', $config['connectionTimeout'] ?? 3.0, $config['readWriteTimeout'] ?? 3.0, $config['context'] ?? null, $config['keepalive'] ?? false, $config['heartbeat'] ?? 0, $config['channelRpcTimeout'] ?? 0.0, $config['sslProtocol'] ?? null);
            if (!$connection->isConnected())
            {
                throw new \RuntimeException(sprintf('AMQP %s connection failed', $poolName));
            }

            return $connection;
        }
    }

    /**
     * 获取连接实例，每个RequestContext中共用一个.
     *
     * @param string $poolName 连接池名称
     *
     * @return \PhpAmqpLib\Connection\AbstractConnection|null
     */
    public static function getInstance($poolName = null)
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getRequestContextResource($poolName)->getInstance();
        }
        else
        {
            $requestContextKey = '__amqp.' . $poolName;
            $requestContext = RequestContext::getContext();
            if (isset($requestContext[$requestContextKey]))
            {
                return $requestContext[$requestContextKey];
            }
            if (null === self::$connections)
            {
                self::$connections = Config::get('@app.amqp.connections');
            }
            $config = self::$connections[$poolName] ?? null;
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found amqp config %s', $poolName));
            }
            /** @var AbstractConnection|null $connection */
            $connection = App::get($requestContextKey);
            if (null === $connection || !$connection->isConnected())
            {
                /** @var AbstractConnection $connection */
                $connection = App::getBean($config['connectionClass'] ?? AMQPStreamConnection::class, $config['host'], $config['port'], $config['user'], $config['password'], $config['vhost'] ?? '/', $config['insist'] ?? false, $config['loginMethod'] ?? 'AMQPLAIN', $config['loginResponse'] ?? null, $config['locale'] ?? 'en_US', $config['connectionTimeout'] ?? 3.0, $config['readWriteTimeout'] ?? 3.0, $config['context'] ?? null, $config['keepalive'] ?? false, $config['heartbeat'] ?? 0, $config['channelRpcTimeout'] ?? 0.0, $config['sslProtocol'] ?? null);
                if (!$connection->isConnected())
                {
                    throw new \RuntimeException(sprintf('AMQP %s connection failed', $poolName));
                }
                App::set($requestContextKey, $connection);
            }

            return $requestContext[$requestContextKey] = $connection;
        }
    }

    /**
     * 释放连接实例.
     *
     * @param \PhpAmqpLib\Connection\AbstractConnection $client
     *
     * @return void
     */
    public static function release($client)
    {
        $resource = RequestContext::get('poolResources.' . spl_object_id($client));
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池名称.
     *
     * @param string $poolName
     *
     * @return string
     */
    public static function parsePoolName($poolName = null)
    {
        if (null === $poolName)
        {
            $poolName = static::getDefaultPoolName();
        }

        return $poolName;
    }

    /**
     * 获取默认池子名称.
     *
     * @return string
     */
    public static function getDefaultPoolName()
    {
        // @phpstan-ignore-next-line
        return App::getBean('AMQP')->getDefaultPoolName();
    }
}
