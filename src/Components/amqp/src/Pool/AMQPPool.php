<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\App;
use Imi\Pool\PoolManager;
use Imi\RequestContext;

/**
 * AMQP 客户端连接池.
 */
class AMQPPool
{
    /**
     * 获取新的连接实例.
     *
     * @param string $poolName 连接池名称
     *
     * @return \PhpAmqpLib\Connection\AbstractConnection
     */
    public static function getNewInstance($poolName = null)
    {
        return PoolManager::getResource(static::parsePoolName($poolName))->getInstance();
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
        return PoolManager::getRequestContextResource(static::parsePoolName($poolName))->getInstance();
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
        $resource = RequestContext::get('poolResources.' . spl_object_hash($client));
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
        return App::getBean('AMQP')->getDefaultPoolName();
    }
}
